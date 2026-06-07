<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseService
{
    /**
     * Call stored procedure: userScanHistory (returns scans + total count)
     */
    public function getUserScans(int $userId, int $limit = 10, int $offset = 0): array
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("CALL userScanHistory(:user_id, :p_limit, :p_offset)");
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->bindParam(':p_limit', $limit, \PDO::PARAM_INT);
            $stmt->bindParam(':p_offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            $scans = $stmt->fetchAll(\PDO::FETCH_OBJ);
            $stmt->nextRowset();
            $total = $stmt->fetchColumn();
            $stmt->closeCursor();

            return [
                'data'  => $scans,
                'total' => (int) $total,
            ];
        } catch (\Exception $e) {
            Log::error("userScanHistory failed: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * Call stored procedure: getDiseaseStatistics
     */
    public function getDiseaseStatistics(string $startDate, string $endDate): array
    {
        try {
            return DB::select("CALL getDiseaseStatistics(?, ?)", [$startDate, $endDate]);
        } catch (\Exception $e) {
            Log::error("getDiseaseStatistics failed: " . $e->getMessage());
            return [];
        }
    }

    // -------------------- Views --------------------

    /**
     * View: user_scan_statistics – returns one row per user
     */
    public function getUserStats(int $userId): ?object
    {
        return DB::table('user_scan_statistics')
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * View: admin_dashboard_stats – single row with totals
     */
    public function getAdminStats(): ?object
    {
        return DB::table('admin_dashboard_stats')->first();
    }

    /**
     * View: recent_scans_details – latest scans with user info
     */
    public function getRecentScans(int $limit = 20): array
    {
        return DB::table('recent_scans_details')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * View: disease_summary – aggregated by disease and date
     */
    public function getDiseaseSummary(?int $days = 30): array
    {
        $query = DB::table('disease_summary')
            ->whereNotNull('disease_type');
        if ($days) {
            $query->where('detection_date', '>=', now()->subDays($days));
        }
        return $query->orderBy('detection_date', 'desc')->get()->all();
    }

    /**
     * Direct query (no stored procedure) for disease distribution of a user
     * (Uses the existing indexes)
     */
    public function getUserDiseaseDistribution(int $userId): array
    {
        return DB::table('scan_histories')
            ->select('disease_type', DB::raw('COUNT(*) as count'))
            ->where('user_id', $userId)
            ->groupBy('disease_type')
            ->orderByDesc('count')
            ->get()
            ->all();
    }
}