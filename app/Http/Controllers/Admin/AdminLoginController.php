<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // If validation fails, redirect back with errors and old input
        if ($validator->fails()) {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Manually check credentials
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (Auth::guard('admin')->attempt($credentials)) {

            $admin= Auth::guard('admin')->user();

            if ($admin->role==2) {
                return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'You are logged in successfully!');
            } else{
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')
                ->with('error', 'You are not authorize to access admin panel.');
            }


        } else {
            // Authentication failed, redirect back with error message
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->only('email'))
                ->with('error', 'Login failed, please check your credentials.');
        }
    }


}
