<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('frontend.auth.login');
    }

    public function register()
    {
        return view('frontend.auth.register');
    }

    public function registerStore(Request $request)
    {
        // Validate the request

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15', // Validate phone number
            'password' => 'required|string|min:5|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, // Save phone number to the database
            'password' => Hash::make($request->password),
        ]);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function authenticate(Request $request)
    {
        // Validate the login request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // Regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();

            if (session()->has('url.intended')) {
                return redirect(session()->get('url.intended'));
            }

            // Redirect to the intended page (or a default route like the dashboard)
            return redirect()->route('profile')->with('success', 'Logged in successfully!');
        }

        // If login fails, redirect back with an error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email'); // Keep the email input value
    }

    public function profile()
    {
        $user = Auth::user();
        $countries = Country::all();
        $user_address = CustomerAddress::where('user_id', $user->id)->first();
        return view('frontend.auth.profile', compact('user', 'countries', 'user_address'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $owner = User::where('id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $owner->id,
            'phone' => 'required|string|max:15',
        ]);

        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->phone = $request->phone;

        $owner->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully!',
        ]);
    }

    public function updateAddress(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'country_id' => 'required|exists:countries,id',
            'address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
        ]);

        // Get or create the user's address
        $userAddress = CustomerAddress::firstOrNew(['user_id' => Auth::id()]);

        // Update the user's address information
        $userAddress->first_name = $request->first_name;
        $userAddress->last_name = $request->last_name;
        $userAddress->email = $request->email;
        $userAddress->phone = $request->phone;
        $userAddress->address = $request->address;
        $userAddress->apartment = $request->apartment;
        $userAddress->country_id = $request->country_id;
        $userAddress->city = $request->city;
        $userAddress->state = $request->state;
        $userAddress->zip = $request->zip;

        // Save changes to the database
        $userAddress->save();

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully!',
        ]);
    }

    public function logout(Request $request)
    {
        // Log the user out of the application
        Auth::logout();

        // Invalidate the session to prevent reuse
        $request->session()->invalidate();

        // Regenerate the session token to prevent CSRF attacks
        $request->session()->regenerateToken();

        // Redirect to the login page or home page with a success message
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function myOrders()
    {

        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
        return view('frontend.auth.order', compact('orders'));
    }

    public function orderDetails($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->first();

        $order_items = OrderItem::where('order_id', $order->id)->with('product')->get();
        return view('frontend.auth.order-detail', compact('order', 'order_items'));
    }

    public function changePassword()
    {
        return view('frontend.auth.change-password');
    }

    public function changePasswordSave(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed', // Ensure new_password and confirm_password match
        ]);

        $user = User::where('id', Auth::user()->id)->first();

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

        Auth::logout();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully! You will be redirected to the login page.',
            'redirect' => route('login'), // Send the login route for redirection
        ]);
    }

    public function forgotPassword()
    {
        return view('frontend.auth.forget-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // return validation errors
                ->withInput(); // return input to the form
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'=>$request->email,
            'token'=>$token,
            'created_at'=>now()
        ]);

        $user = User::where('email',$request->email)->first();

        $formData = [
            'token'=>$token,
            'user' =>$user,
        ];

        Mail::to($request->email)->send(new ResetPasswordMail($formData));

        session()->flash('success', 'Password reset token sent to your email.');

        return redirect()->back();
    }


    public function resetPassword($token){
        $tokenExist = DB::table('password_reset_tokens')->where('token',$token)->first();
        if ($tokenExist==null) {
            return redirect()->route('user.forget_password')->with('error','Invalid request');
        }
        return view('frontend.auth.reset-password',[
            'token'=>$token
        ]);
    }

    public function processResetPassword(Request $request){
        $token = $request->token;
        $tokenObj = DB::table('password_reset_tokens')->where('token',$token)->first();
        if ($tokenObj==null) {
            return redirect()->route('user.forget_password')->with('error','Invalid request');
        }

        $user = User::where('email',$tokenObj->email)->first();

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->route('user.reset_password',$token)
                ->withErrors($validator) // return validation errors
                ->withInput(); // return input to the form
        }

        User::where('id',$user->id)->update([
            'password'=>Hash::make($request->new_password)
        ]);

        DB::table('password_reset_tokens')->where('email',$user->email)->delete();

        return redirect()->route('login')->with('success','Your password reset successfully');
    }

}
