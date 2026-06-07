<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add foreign key constraints
        $this->addForeignKeys();
        
        // 2. Add missing columns
        $this->addMissingColumns();
        
        // 3. Create optimized indexes
        $this->createOptimizedIndexes();
        
        // 4. Create audit_logs table (optional)
        $this->createAuditLogsTable();
        
        // 5. Create views
        $this->createViews();
        
        // 6. Create stored procedures (only the ones you want)
        $this->createStoredProcedures();
        
        // 7. Create triggers
        $this->createTriggers();
    }
    
    private function addForeignKeys()
    {
        // Add foreign key for sessions table if not exists
        try {
            DB::statement("
                ALTER TABLE `sessions` 
                ADD CONSTRAINT `sessions_user_id_foreign` 
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ");
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }
    
    private function addMissingColumns()
    {
        if (!Schema::hasColumn('users', 'last_activity')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_activity')->nullable()->after('updated_at');
            });
        }
    }
    
    private function createOptimizedIndexes()
    {
        // Composite indexes for better query performance
        $indexes = [
            // For scan_histories table
            'idx_scan_user_created' => 
                "CREATE INDEX idx_scan_user_created ON scan_histories(user_id, created_at DESC)",
            'idx_scan_disease_confidence' => 
                "CREATE INDEX idx_scan_disease_confidence ON scan_histories(disease_type, confidence)",
            'idx_scan_created_date' => 
                "CREATE INDEX idx_scan_created_date ON scan_histories(DATE(created_at))",
            
            // For users table
            'idx_users_email_name' => 
                "CREATE INDEX idx_users_email_name ON users(email, name)",
            'idx_users_admin_created' => 
                "CREATE INDEX idx_users_admin_created ON users(is_admin, created_at DESC)",
            'idx_users_last_activity' => 
                "CREATE INDEX idx_users_last_activity ON users(last_activity)",
            
            // For jobs table
            'idx_jobs_reserved_available' => 
                "CREATE INDEX idx_jobs_reserved_available ON jobs(reserved_at, available_at)",
            'idx_jobs_queue_attempts' => 
                "CREATE INDEX idx_jobs_queue_attempts ON jobs(queue, attempts)",
            
            // For cache and sessions
            'idx_sessions_last_activity' => 
                "CREATE INDEX idx_sessions_last_activity ON sessions(last_activity)",
        ];
        
        foreach ($indexes as $name => $sql) {
            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                // Index might already exist - log quietly
                \Illuminate\Support\Facades\Log::info("Index {$name} already exists or creation failed");
            }
        }
    }
    
    private function createAuditLogsTable()
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('table_name', 100);
                $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
                $table->unsignedBigInteger('record_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->timestamp('created_at')->useCurrent();
                
                $table->index(['table_name', 'record_id']);
                $table->index('user_id');
                $table->index('created_at');
            });
        }
    }
    
    private function createViews()
    {
        // View 1: User Scan Statistics
        DB::statement("DROP VIEW IF EXISTS user_scan_statistics");
        DB::statement("
            CREATE VIEW user_scan_statistics AS
            SELECT 
                u.id AS user_id,
                u.name,
                u.email,
                COUNT(sh.id) AS total_scans,
                COUNT(DISTINCT sh.disease_type) AS unique_diseases_detected,
                MAX(sh.created_at) AS last_scan_date,
                MIN(sh.created_at) AS first_scan_date,
                COALESCE(AVG(sh.confidence), 0) AS avg_confidence,
                COALESCE(MAX(sh.confidence), 0) AS highest_confidence
            FROM users u
            LEFT JOIN scan_histories sh ON u.id = sh.user_id
            GROUP BY u.id, u.name, u.email
        ");
        
        // View 2: Disease Summary
        DB::statement("DROP VIEW IF EXISTS disease_summary");
        DB::statement("
            CREATE VIEW disease_summary AS
            SELECT 
                disease_type,
                COUNT(*) AS detection_count,
                ROUND(AVG(confidence), 2) AS avg_confidence,
                ROUND(MIN(confidence), 2) AS min_confidence,
                ROUND(MAX(confidence), 2) AS max_confidence,
                COUNT(DISTINCT user_id) AS unique_users_affected,
                DATE(created_at) AS detection_date
            FROM scan_histories
            GROUP BY disease_type, DATE(created_at)
            ORDER BY detection_date DESC, detection_count DESC
        ");
        
        // View 3: Recent Scans with User Details
        DB::statement("DROP VIEW IF EXISTS recent_scans_details");
        DB::statement("
            CREATE VIEW recent_scans_details AS
            SELECT 
                sh.id AS scan_id,
                u.name AS user_name,
                u.email AS user_email,
                sh.disease_type,
                sh.confidence,
                sh.image_path,
                sh.recommendations,
                sh.created_at AS scan_date,
                TIMESTAMPDIFF(HOUR, sh.created_at, NOW()) AS hours_ago,
                DATE_FORMAT(sh.created_at, '%Y-%m-%d %H:%i:%s') AS formatted_date
            FROM scan_histories sh
            INNER JOIN users u ON sh.user_id = u.id
            WHERE sh.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY sh.created_at DESC
        ");
        
        // View 4: Admin Dashboard Overview
        DB::statement("DROP VIEW IF EXISTS admin_dashboard_stats");
        DB::statement("
            CREATE VIEW admin_dashboard_stats AS
            SELECT 
                (SELECT COUNT(*) FROM users) AS total_users,
                (SELECT COUNT(*) FROM scan_histories) AS total_scans,
                (SELECT COUNT(DISTINCT disease_type) FROM scan_histories) AS unique_diseases,
                (SELECT COUNT(*) FROM users WHERE is_admin = 1) AS admin_count,
                (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS new_users_last_week,
                (SELECT COUNT(*) FROM scan_histories WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS scans_last_week,
                (SELECT COALESCE(AVG(confidence), 0) FROM scan_histories WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS avg_confidence_last_week
        ");
        
        // View 5: Daily Scan Trends
        DB::statement("DROP VIEW IF EXISTS daily_scan_trends");
        DB::statement("
            CREATE VIEW daily_scan_trends AS
            SELECT 
                DATE(created_at) AS scan_date,
                COUNT(*) AS total_scans,
                COUNT(DISTINCT user_id) AS unique_users,
                COUNT(DISTINCT disease_type) AS unique_diseases,
                ROUND(AVG(confidence), 2) AS avg_confidence
            FROM scan_histories
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY scan_date DESC
        ");
    }
    
    private function createStoredProcedures()
    {
        // Only the stored procedures you want to keep
        
        // Procedure 1: Get User Scans with Pagination
        DB::statement("DROP PROCEDURE IF EXISTS GetUserScans");
        DB::statement("
            CREATE PROCEDURE GetUserScans(
                IN p_user_id BIGINT,
                IN p_limit INT,
                IN p_offset INT
            )
            BEGIN
                -- Get paginated scans
                SELECT 
                    sh.id,
                    sh.disease_type,
                    sh.confidence,
                    sh.image_path,
                    sh.recommendations,
                    sh.prediction_data,
                    sh.created_at,
                    DATE_FORMAT(sh.created_at, '%Y-%m-%d %H:%i:%s') AS formatted_date
                FROM scan_histories sh
                WHERE sh.user_id = p_user_id
                ORDER BY sh.created_at DESC
                LIMIT p_limit OFFSET p_offset;
                
                -- Get total count for pagination
                SELECT COUNT(*) AS total_records
                FROM scan_histories
                WHERE user_id = p_user_id;
            END
        ");
        
        // Procedure 2: Get Disease Statistics by Date Range
        DB::statement("DROP PROCEDURE IF EXISTS GetDiseaseStatistics");
        DB::statement("
            CREATE PROCEDURE GetDiseaseStatistics(
                IN p_start_date DATE,
                IN p_end_date DATE
            )
            BEGIN
                SELECT 
                    disease_type,
                    COUNT(*) AS detection_count,
                    ROUND(AVG(confidence), 2) AS average_confidence,
                    ROUND(MIN(confidence), 2) AS min_confidence,
                    ROUND(MAX(confidence), 2) AS max_confidence,
                    COUNT(DISTINCT user_id) AS unique_users,
                    GROUP_CONCAT(DISTINCT user_id) AS user_ids
                FROM scan_histories
                WHERE DATE(created_at) BETWEEN p_start_date AND p_end_date
                GROUP BY disease_type
                ORDER BY detection_count DESC;
            END
        ");
        
        // Procedure 3: Get Scan Statistics for a Specific User
        DB::statement("DROP PROCEDURE IF EXISTS GetUserStatistics");
        DB::statement("
            CREATE PROCEDURE GetUserStatistics(IN p_user_id BIGINT)
            BEGIN
                SELECT 
                    COUNT(*) AS total_scans,
                    COUNT(DISTINCT disease_type) AS unique_diseases,
                    MAX(created_at) AS last_scan_date,
                    MIN(created_at) AS first_scan_date,
                    ROUND(AVG(confidence), 2) AS avg_confidence,
                    (
                        SELECT disease_type 
                        FROM scan_histories 
                        WHERE user_id = p_user_id 
                        GROUP BY disease_type 
                        ORDER BY COUNT(*) DESC 
                        LIMIT 1
                    ) AS most_common_disease
                FROM scan_histories
                WHERE user_id = p_user_id;
            END
        ");
        
        // Procedure 4: Get Disease Distribution for a User
        DB::statement("DROP PROCEDURE IF EXISTS GetUserDiseaseDistribution");
        DB::statement("
            CREATE PROCEDURE GetUserDiseaseDistribution(IN p_user_id BIGINT)
            BEGIN
                SELECT 
                    disease_type,
                    COUNT(*) AS count,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM scan_histories WHERE user_id = p_user_id)), 2) AS percentage
                FROM scan_histories
                WHERE user_id = p_user_id
                GROUP BY disease_type
                ORDER BY count DESC;
            END
        ");
    }
    
    private function createTriggers()
    {
        // Drop existing triggers
        DB::statement("DROP TRIGGER IF EXISTS update_scan_histories_timestamp");
        DB::statement("DROP TRIGGER IF EXISTS before_scan_insert");
        DB::statement("DROP TRIGGER IF EXISTS after_user_insert");
        DB::statement("DROP TRIGGER IF EXISTS update_user_last_activity");
        
        // Trigger 1: Auto-update timestamp on scan_histories
        DB::statement("
            CREATE TRIGGER update_scan_histories_timestamp 
            BEFORE UPDATE ON scan_histories
            FOR EACH ROW
            BEGIN
                SET NEW.updated_at = CURRENT_TIMESTAMP;
            END
        ");
        
        // Trigger 2: Auto-generate recommendations if null
        DB::statement("
            CREATE TRIGGER before_scan_insert 
            BEFORE INSERT ON scan_histories
            FOR EACH ROW
            BEGIN
                IF NEW.recommendations IS NULL THEN
                    SET NEW.recommendations = CASE NEW.disease_type
                        WHEN 'Healthy' THEN 'Your plant appears healthy. Continue regular care and monitoring.'
                        WHEN 'Early Blight' THEN 'Remove affected leaves, apply fungicide, and ensure proper air circulation.'
                        WHEN 'Late Blight' THEN 'Immediately remove infected plants, avoid overhead watering, apply copper-based fungicide.'
                        WHEN 'Leaf Curl' THEN 'Control whitefly population, remove infected leaves, apply neem oil.'
                        WHEN 'Powdery Mildew' THEN 'Increase air circulation, apply sulfur-based fungicide, avoid nitrogen-rich fertilizers.'
                        WHEN 'Rust' THEN 'Remove infected leaves, apply fungicide, ensure good air circulation.'
                        WHEN 'Septoria Leaf Spot' THEN 'Remove infected leaves, avoid overhead watering, apply fungicide.'
                        ELSE 'Consult with a local agricultural expert for proper treatment recommendations.'
                    END;
                END IF;
            END
        ");
        
        // Trigger 3: Log new user registration to audit_logs
        DB::statement("
            CREATE TRIGGER after_user_insert 
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, action, record_id, user_id, new_data)
                VALUES ('users', 'INSERT', NEW.id, NEW.id, 
                        JSON_OBJECT('name', NEW.name, 'email', NEW.email, 'phone', NEW.phone));
            END
        ");
        
        // Trigger 4: Update user last_activity when new scan is added
        DB::statement("
            CREATE TRIGGER update_user_last_activity 
            AFTER INSERT ON scan_histories
            FOR EACH ROW
            BEGIN
                UPDATE users 
                SET last_activity = CURRENT_TIMESTAMP
                WHERE id = NEW.user_id;
            END
        ");
    }
    
    public function down()
    {
        // Drop triggers
        DB::statement("DROP TRIGGER IF EXISTS update_scan_histories_timestamp");
        DB::statement("DROP TRIGGER IF EXISTS before_scan_insert");
        DB::statement("DROP TRIGGER IF EXISTS after_user_insert");
        DB::statement("DROP TRIGGER IF EXISTS update_user_last_activity");
        
        // Drop procedures
        DB::statement("DROP PROCEDURE IF EXISTS GetUserScans");
        DB::statement("DROP PROCEDURE IF EXISTS GetDiseaseStatistics");
        DB::statement("DROP PROCEDURE IF EXISTS GetUserStatistics");
        DB::statement("DROP PROCEDURE IF EXISTS GetUserDiseaseDistribution");
        
        // Drop views
        DB::statement("DROP VIEW IF EXISTS user_scan_statistics");
        DB::statement("DROP VIEW IF EXISTS disease_summary");
        DB::statement("DROP VIEW IF EXISTS recent_scans_details");
        DB::statement("DROP VIEW IF EXISTS admin_dashboard_stats");
        DB::statement("DROP VIEW IF EXISTS daily_scan_trends");
        
        // Drop foreign key constraint
        try {
            DB::statement("ALTER TABLE sessions DROP FOREIGN KEY sessions_user_id_foreign");
        } catch (\Exception $e) {
            // Constraint might not exist
        }
        
        // Drop indexes
        $indexes = [
            'scan_histories' => [
                'idx_scan_user_created',
                'idx_scan_disease_confidence',
                'idx_scan_created_date'
            ],
            'users' => [
                'idx_users_email_name',
                'idx_users_admin_created',
                'idx_users_last_activity'
            ],
            'jobs' => [
                'idx_jobs_reserved_available',
                'idx_jobs_queue_attempts'
            ],
            'sessions' => [
                'idx_sessions_last_activity'
            ]
        ];
        
        foreach ($indexes as $table => $indexList) {
            foreach ($indexList as $index) {
                try {
                    DB::statement("DROP INDEX {$index} ON {$table}");
                } catch (\Exception $e) {
                    // Index might not exist
                }
            }
        }
        
        // Drop audit_logs table
        Schema::dropIfExists('audit_logs');
        
        // Drop last_activity column
        if (Schema::hasColumn('users', 'last_activity')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_activity');
            });
        }
    }
};