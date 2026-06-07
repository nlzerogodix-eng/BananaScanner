<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanHistory extends Model
{
    use HasFactory;

    protected $table = 'scan_histories';

    protected $fillable = [
        'user_id',
        'image_path',
        'disease_type',
        'confidence',
        'prediction_data',
        'recommendations',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'prediction_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the scan history
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Call stored procedure to get user statistics
     */
    public static function getUserStatistics($userId)
    {
        try {
            $results = DB::select('CALL GetUserScanStatistics(?)', [$userId]);
            
            // MySQL returns multiple result sets
            $statistics = [
                'basic_stats' => isset($results[0]) ? (array)$results[0] : null,
                'disease_distribution' => isset($results[1]) ? $results[1] : []
            ];
            
            return $statistics;
        } catch (\Exception $e) {
            Log::error('Error calling stored procedure GetUserScanStatistics: ' . $e->getMessage());
            return [
                'basic_stats' => null,
                'disease_distribution' => [],
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Search scan histories with pagination using stored procedure
     */
    public static function advancedSearch($filters, $page = 1, $perPage = 15)
    {
        try {
            $results = DB::select('CALL SearchScanHistories(?, ?, ?, ?, ?, ?)', [
                $filters['disease'] ?? null,
                $filters['start_date'] ?? null,
                $filters['end_date'] ?? null,
                $filters['min_confidence'] ?? null,
                $page,
                $perPage
            ]);
            
            $data = isset($results[0]) ? $results[0] : [];
            $total = isset($results[1]) ? $results[1]->total_records ?? 0 : 0;
            
            return [
                'data' => $data,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('Error in advanced search: ' . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get admin dashboard stats using stored procedure
     */
    public static function getAdminStats()
    {
        try {
            return DB::select('CALL GetAdminDashboardStats()');
        } catch (\Exception $e) {
            Log::error('Error getting admin stats: ' . $e->getMessage());
            return [];
        }
    }
}