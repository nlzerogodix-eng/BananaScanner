<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function login() 
    {
        if (Auth::check()) {
            return redirect()->route('home')->with('info', 'You are already logged in.');
        }

        return view('login');
    }
    
    public function register() 
    {
        if (Auth::check()) {
            return redirect()->route('home')->with('info', 'You are already logged in.');
        }

        return view('register');
    }
    
    public function registerValidate(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'], // The 'hashed' cast handles hashing automatically
            ]);
            
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'Registration successful! Welcome to Banana Scan!');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }

    public function loginValidate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verifying the user credentials
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Login successful! Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'profile_picture.max' => 'The profile picture must not be larger than 10MB.',
            'profile_picture.mimes' => 'Only JPEG, PNG, JPG, GIF, and WEBP images are allowed.',
        ]);
        
        $currentUser = Auth::user();
        $oldProfilePicture = $currentUser->profile_picture;
        
        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'location' => $validated['location'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = uniqid() . '_' . $userId . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profile_pictures');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $filename);
            $updateData['profile_picture'] = $filename;
            
            if ($oldProfilePicture) {
                $oldFilePath = public_path('uploads/profile_pictures/' . $oldProfilePicture);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }
        
        if ($request->filled('password')) {
            $updateData['password'] = $validated['password']; // Cast handles hashing
        }
        
        $updated = DB::table('users')
            ->where('id', $userId)
            ->update($updateData);
        
        if ($updated) {
            Auth::loginUsingId($userId);
            $request->session()->regenerate();
            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->route('profile.edit')
                ->with('error', 'No changes were made.')
                ->withInput();
        }
    }

    public function create(array $data) {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('login')->with('success', 'You have been logged out successfully.');
    }
}

