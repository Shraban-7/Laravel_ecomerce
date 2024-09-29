<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function changePassword()
    {
        return view('admin.setting');
    }

    public function changePasswordSave(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed', // Ensure new_password and confirm_password match
        ]);

        $user = User::where('id',Auth::guard('admin')->user()->id)->first() ;

        // Check if old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password does not match our records.',
            ], 422);
        }

        // Update the new password
        $user->password = Hash::make($request->new_password);
        $user->save();


        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully! You will be redirected to the login page.', // Send the login route for redirection
        ]);
    }
}
