<?php

namespace App\Http\Controllers;

use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ScanHistory;
use App\Http\Controllers\Controller;

class ScanController extends Controller
{
    protected $db;
    
    public function __construct(DatabaseService $db)
    {
        $this->db = $db;
        $this->middleware('auth');
    }
    
    // User dashboard using stored procedures and views
    public function dashboard()
    {
        $userId = Auth::id();
        $page = request('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $scansResult = $this->db->getUserScans($userId, $perPage, $offset);
        $userStats   = $this->db->getUserStats($userId);
        $distribution = $this->db->getUserDiseaseDistribution($userId);

        return view('dashboard', [
            'scans'       => $scansResult['data'],      // array of scan objects
            'totalScans'  => $scansResult['total'],
            'currentPage' => $page,
            'perPage'     => $perPage,
            'stats'       => $userStats,
            'distribution'=> $distribution,
        ]);
    }
    
    // Admin dashboard using views
    public function adminDashboard()
    {
        if (!Auth::user()->is_admin) abort(403);

        $adminStats   = $this->db->getAdminStats();
        $recentScans  = $this->db->getRecentScans(20);
        $diseaseSummary = $this->db->getDiseaseSummary(30);
        $diseaseStats = $this->db->getDiseaseStatistics(
            now()->subDays(30)->toDateString(),
            now()->toDateString()
        );

        // Optional: top users via direct query
        $topUsers = DB::table('users as u')
            ->select('u.id', 'u.name', 'u.email', DB::raw('COUNT(sh.id) as total_scans'))
            ->leftJoin('scan_histories as sh', 'u.id', '=', 'sh.user_id')
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('total_scans')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'adminStats', 'recentScans', 'diseaseSummary', 'diseaseStats', 'topUsers'
        ));
    }
    
    // Store scan - triggers will run automatically
    public function store(Request $request)
    {
        $scanId = DB::table('scan_histories')->insertGetId([
            'user_id' => Auth::id(),
            'disease_type' => $request->disease_type,
            'confidence' => $request->confidence,
            'image_path' => $request->image_path,
            'prediction_data' => json_encode($request->prediction_data),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Your triggers automatically:
        // - Add recommendations (before_scan_insert)
        // - Update user last_activity (update_user_last_activity)
        // - Log to audit_logs (if you have after_user_insert trigger)
        
        return redirect()->back()->with('success', 'Scan saved successfully');
    }
    
    // Search using direct query (no stored procedure needed)
    public function search(Request $request)
    {
        $userId = Auth::user()->is_admin ? null : Auth::id();
        
        $query = DB::table('scan_histories as sh')
            ->join('users as u', 'sh.user_id', '=', 'u.id')
            ->select('sh.*', 'u.name', 'u.email')
            ->where('sh.disease_type', 'LIKE', "%{$request->search}%");
        
        if ($userId) {
            $query->where('sh.user_id', $userId);
        }
        
        return response()->json($query->limit(20)->get());
    }
    
    // Cleanup old data (admin only)
    public function cleanup()
    {
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(30)->timestamp)
            ->delete();
            
        DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(30))
            ->delete();
            
        return response()->json(['deleted_sessions' => $deleted]);
    }
    
    public function showModel()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return view('model');
    }

    public function processInference(Request $request)
    {
        // Add manual auth check
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized. Please login.'], 401);
        }
        
        // Increase execution time
        set_time_limit(300);
        
        // Validate the request
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'confidence' => 'nullable|numeric|min:0|max:100',
            'classes' => 'nullable|string'
        ]);

        $apiKey = config('roboflow.api_key');
        $workflowUrl = config('roboflow.workflow_endpoint');
        
        $minConfidence = $request->input('confidence', 50);
        $classFilter = $request->input('classes');
        
        $results = [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    // Read and encode the image
                    $imageData = base64_encode(file_get_contents($image->getPathname()));
                    
                    // Prepare payload
                    $payload = [
                        'api_key' => $apiKey,
                        'inputs' => [
                            'image' => [
                                'type' => 'base64',
                                'value' => 'data:image/jpeg;base64,' . $imageData
                            ]
                        ],
                        'visualization' => [
                            'format' => 'image'
                        ]
                    ];

                    // Send to Roboflow
                    $response = Http::timeout(60)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($workflowUrl, $payload);

                    if ($response->successful()) {
                        $prediction = $response->json();
                        
                        // Extract disease type and confidence
                        $diseaseInfo = $this->extractDiseaseInfo($prediction);
                        
                        // Save the original image
                        $filename = uniqid() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('uploads/scans');
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0777, true);
                        }
                        
                        // Move the uploaded image
                        $image->move($destinationPath, $filename);
                        
                        // Save scan history with minimal data
                        $storedData = [
                            'summary' => $diseaseInfo['prediction_summary'],
                            'disease_type' => $diseaseInfo['disease_type'],
                            'confidence' => $diseaseInfo['confidence'],
                            'full_prediction_stored' => false
                        ];

                        $scan = ScanHistory::create([
                            'user_id' => Auth::id(),
                            'image_path' => $filename,
                            'disease_type' => $diseaseInfo['disease_type'],
                            'confidence' => $diseaseInfo['confidence'],
                            'prediction_data' => json_encode($storedData), // Store minimal data
                        ]);

                        $results[] = [
                            'filename' => $image->getClientOriginalName(),
                            'prediction' => $prediction,
                            'scan_id' => $scan->id // Return the scan ID
                        ];
                    } else {
                        $results[] = [
                            'filename' => $image->getClientOriginalName(),
                            'error' => 'API Error: ' . $response->status()
                        ];
                    }
                    
                } catch (\Exception $e) {
                    $results[] = [
                        'filename' => $image->getClientOriginalName(),
                        'error' => 'Processing error: ' . $e->getMessage()
                    ];
                    Log::error('Inference error: ' . $e->getMessage());
                }
            }
        }

        return response()->json($results);
    }
    
    /**
     * Extract disease type and confidence from prediction
     */
    private function extractDiseaseInfo($prediction)
    {
        $diseaseType = 'Unknown';
        $confidence = 0;
        $predictionSummary = [];
        
        // Check the structure of the prediction response
        if (isset($prediction['outputs']) && is_array($prediction['outputs'])) {
            foreach ($prediction['outputs'] as $output) {
                if (isset($output['predictions']['predictions']) && is_array($output['predictions']['predictions'])) {
                    $predictions = $output['predictions']['predictions'];
                    
                    // Initialize variables to track the best prediction
                    $bestPrediction = null;
                    $bestConfidence = 0;
                    $allPredictions = [];
                    
                    foreach ($predictions as $pred) {
                        if (isset($pred['confidence'])) {
                            $predConfidence = round($pred['confidence'] * 100, 2);
                            $allPredictions[] = [
                                'class' => $pred['class'] ?? 'Unknown',
                                'confidence' => $predConfidence
                            ];
                            
                            if ($pred['confidence'] > $bestConfidence) {
                                $bestConfidence = $pred['confidence'];
                                $bestPrediction = $pred;
                            }
                        }
                    }
                    
                    if ($bestPrediction) {
                        $confidence = round($bestConfidence * 100, 2);
                        
                        // Determine disease type based on class name
                        if (isset($bestPrediction['class'])) {
                            $className = strtolower($bestPrediction['class']);
                            
                            if (strpos($className, 'panama') !== false) {
                                $diseaseType = 'Panama';
                            } elseif (strpos($className, 'sigatoka') !== false) {
                                $diseaseType = 'Sigatoka';
                            } elseif (strpos($className, 'healthy') !== false) {
                                $diseaseType = 'Healthy';
                            } else {
                                $diseaseType = ucfirst($className);
                            }
                        }
                        
                        // Store only essential data (much smaller than full prediction)
                        $predictionSummary = [
                            'top_prediction' => [
                                'class' => $bestPrediction['class'] ?? null,
                                'confidence' => $confidence
                            ],
                            'all_predictions' => $allPredictions,
                            'total_predictions' => count($predictions),
                            'timestamp' => now()->toDateTimeString()
                        ];
                    }
                }
            }
        }
        
        return [
            'disease_type' => $diseaseType,
            'confidence' => $confidence,
            'prediction_summary' => $predictionSummary
        ];
    }
    
    /**
     * Get scan details for admin panel
     */
    public function getScanDetails($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $scan = ScanHistory::with('user')->findOrFail($id);
        
        // Get disease color based on disease type
        $diseaseColor = 'secondary'; // default
        if ($scan->disease_type === 'Healthy') {
            $diseaseColor = 'success';
        } elseif ($scan->disease_type === 'Sigatoka') {
            $diseaseColor = 'warning';
        } elseif ($scan->disease_type === 'Panama') {
            $diseaseColor = 'danger';
        }
        
        // Decode prediction data
        $predictionData = $scan->prediction_data ? json_decode($scan->prediction_data, true) : null;
        
        return response()->json([
            'id' => $scan->id,
            'user_name' => $scan->user->name,
            'user_email' => $scan->user->email,
            'disease_type' => $scan->disease_type,
            'disease_color' => $diseaseColor,
            'confidence' => $scan->confidence,
            'image_path' => $scan->image_path,
            'image_url' => $scan->image_path ? asset('uploads/scans/' . $scan->image_path) : null,
            'created_at' => $scan->created_at->format('M d, Y H:i'),
            'prediction_data' => $predictionData
        ]);
    }
}