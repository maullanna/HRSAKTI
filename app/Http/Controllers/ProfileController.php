<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    /**
     * Show the profile page
     */
    public function show()
    {
        $user = Auth::guard('employee')->check() 
            ? Auth::guard('employee')->user() 
            : Auth::user();
        
        return view('profile.index', compact('user'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $isEmployee = Auth::guard('employee')->check();
        $user = $isEmployee 
            ? Auth::guard('employee')->user() 
            : Auth::user();

        // Validation rules based on user type
        if ($isEmployee) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $user->id_employees . ',id_employees',
                'phone' => 'nullable|string|max:20',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id . ',id',
                'phone' => 'nullable|string|max:20',
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('phone') && isset($user->phone)) {
            $user->phone = $request->phone;
        }
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $isEmployee = Auth::guard('employee')->check();
        $user = $isEmployee 
            ? Auth::guard('employee')->user() 
            : Auth::user();

        // Delete old photo if exists
        if ($user->photo && Storage::disk('public')->exists('profiles/' . $user->photo)) {
            Storage::disk('public')->delete('profiles/' . $user->photo);
        }

        // Upload new photo
        $photo = $request->file('photo');
        $userId = $isEmployee ? $user->id_employees : $user->id;
        $photoName = time() . '_' . $userId . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs('profiles', $photoName, 'public');

        $user->photo = $photoName;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Photo uploaded successfully!');
    }

    /**
     * Update password/pin code
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:4|confirmed',
        ]);

        $isEmployee = Auth::guard('employee')->check();
        $user = $isEmployee 
            ? Auth::guard('employee')->user() 
            : Auth::user();

        // Check current password - employees use pin_code, users use password
        $currentPasswordField = $isEmployee ? 'pin_code' : 'password';
        $currentPassword = $user->$currentPasswordField;
        
        if (!Hash::check($request->current_password, $currentPassword)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password - employees use pin_code, users use password
        if ($isEmployee) {
            $user->pin_code = Hash::make($request->new_password);
        } else {
            $user->password = Hash::make($request->new_password);
        }
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Password updated successfully!');
    }
}
