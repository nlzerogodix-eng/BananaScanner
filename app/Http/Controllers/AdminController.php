<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ScanHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Check if user is admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $totalUsers = User::count();
        $totalScans = ScanHistory::count();
        $recentScans = ScanHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        $recentUsers = User::where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get scan statistics by disease type
        $scanStats = ScanHistory::select('disease_type', DB::raw('count(*) as total'))
            ->groupBy('disease_type')
            ->get();
        
        // Get monthly user activity (scans per day for current month)
        $monthlyActivity = $this->getMonthlyActivityData();
        
        // Get user registration data for current month
        $monthlyRegistrations = $this->getMonthlyRegistrationsData();

        return view('admin.home', compact(
            'totalUsers', 
            'totalScans', 
            'recentScans', 
            'recentUsers',
            'scanStats',
            'monthlyActivity',
            'monthlyRegistrations'
        ));
    }

    private function getMonthlyActivityData()
    {
        // Get data for the last 30 days
        $endDate = now();
        $startDate = now()->subDays(29);
        
        $activityData = [];
        $dateLabels = [];
        
        // Initialize all dates with 0 scans
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('M d');
            $dateLabels[] = $dateStr;
            $activityData[$dateStr] = 0;
        }
        
        // Get actual scan counts
        $scans = ScanHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Fill in actual data
        foreach ($scans as $scan) {
            $dateStr = \Carbon\Carbon::parse($scan->date)->format('M d');
            $activityData[$dateStr] = $scan->count;
        }
        
        return [
            'labels' => $dateLabels,
            'data' => array_values($activityData),
            'total' => array_sum(array_values($activityData)),
            'average' => round(array_sum(array_values($activityData)) / count($activityData), 1)
        ];
    }

    private function getMonthlyRegistrationsData()
    {
        // Get data for the last 30 days
        $endDate = now();
        $startDate = now()->subDays(29);
        
        $registrationData = [];
        $dateLabels = [];
        
        // Initialize all dates with 0 registrations
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('M d');
            $dateLabels[] = $dateStr;
            $registrationData[$dateStr] = 0;
        }
        
        // Get actual registration counts
        $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('is_admin', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Fill in actual data
        foreach ($registrations as $reg) {
            $dateStr = \Carbon\Carbon::parse($reg->date)->format('M d');
            $registrationData[$dateStr] = $reg->count;
        }
        
        return [
            'labels' => $dateLabels,
            'data' => array_values($registrationData),
            'total' => array_sum(array_values($registrationData))
        ];
    }

    public function users()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Get only regular users (non-admin)
        $users = User::where('is_admin', false)
            ->withCount('scanHistories')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    // Store new user (created by admin)
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'email_verified' => 'nullable|in:0,1',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'email_verified_at' => $request->input('email_verified') == '1' ? now() : null,
                'is_admin' => false,
            ]);

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = uniqid() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/profile_pictures');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                $file->move($destinationPath, $filename);
                $user->profile_picture = $filename;
                $user->save();
            }

            return redirect()->route('admin.users')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create user. Please try again.')->withInput();
        }
    }

    // Show edit user form (optional, if you want a separate page)
    public function edit($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        // Prevent editing admin users
        if ($user->is_admin) {
            return redirect()->route('admin.users')->with('error', 'Cannot edit admin accounts.');
        }
        
        return view('admin.users-edit', compact('user')); // Optional if you want a separate page
    }

    // Update user
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        if ($user->is_admin) {
            return redirect()->route('admin.users')->with('error', 'Cannot edit admin accounts.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:10240',
            'email_verified' => 'nullable|in:0,1',
        ]);

        try {
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            $user->location = $validated['location'] ?? null;
            
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }
            
            if ($request->input('email_verified') == '1') {
                $user->email_verified_at = now();
            } else {
                $user->email_verified_at = null;
            }
            
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = uniqid() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/profile_pictures');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                if ($user->profile_picture) {
                    $oldFilePath = public_path('uploads/profile_pictures/' . $user->profile_picture);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                $file->move($destinationPath, $filename);
                $user->profile_picture = $filename;
            }
            
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user. Please try again.')->withInput();
        }
    }

    public function scans()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $scans = ScanHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.scans', compact('scans'));
    }

    public function deleteUser($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        // Prevent deleting admin users
        if ($user->is_admin) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete admin accounts.');
        }

        // Delete user's profile picture if exists
        if ($user->profile_picture) {
            $filePath = public_path('uploads/profile_pictures/' . $user->profile_picture);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    public function deleteScan($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $scan = ScanHistory::findOrFail($id);
        $scan->delete();

        return redirect()->route('admin.scans')->with('success', 'Scan record deleted successfully!');
    }

    public function stats()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Weekly statistics (last 7 days)
        $weeklyStats = ScanHistory::select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->get();

        // Disease distribution
        $diseaseStats = ScanHistory::select('disease_type', DB::raw('COUNT(*) as count'))
            ->groupBy('disease_type')
            ->get();

        // User activity (top 10 users by scan count)
        $activeUsers = User::withCount('scanHistories')
            ->where('is_admin', false)
            ->with(['scanHistories' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->has('scanHistories')
            ->orderBy('scan_histories_count', 'desc')
            ->take(10)
            ->get();

        // Additional statistics
        $totalScans = ScanHistory::count();
        $avgConfidence = ScanHistory::avg('confidence') ?? 0;
        
        // Active users in last 30 days
        $activeUsersCount = User::where('is_admin', false)
            ->whereHas('scanHistories', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count();

        // Disease detection rate (non-healthy scans)
        $totalDiseaseScans = ScanHistory::where('disease_type', '!=', 'Healthy')->count();
        $diseaseRate = $totalScans > 0 ? ($totalDiseaseScans / $totalScans) * 100 : 0;

        return view('admin.stats', compact(
            'weeklyStats', 
            'diseaseStats', 
            'activeUsers',
            'totalScans',
            'avgConfidence',
            'activeUsersCount',
            'diseaseRate'
        ));
    }
    
    public function editAdminProfile()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }
        
        $admin = Auth::user();
        return view('admin.edit-profile', compact('admin'));
    }
    
    public function updateAdminProfile(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }
        
        $userId = Auth::id();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'current_password' => 'required',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);
        
        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }
        
        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];
        
        if ($request->filled('new_password')) {
            $updateData['password'] = $validated['new_password']; // Cast handles hashing
        }
        
        $updated = User::where('id', $userId)->update($updateData);
        
        if ($updated) {
            Auth::loginUsingId($userId);
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard')->with('success', 'Admin profile updated successfully!');
        }
        
        return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
    }
}