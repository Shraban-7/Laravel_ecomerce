<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // app/Http/Controllers/Admin/UserController.php

    public function index(Request $request)
    {
        // Get the search keyword from the request
        $keyword = $request->get('keyword');

        // Query users based on the search keyword
        $users = User::where('role', 1)
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.user.list', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|max:15',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']), // Hash the password
        ]);

        // Return a success response
        return response()->json(['status' => true, 'message' => 'User created successfully!', 'user' => $user]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', compact('user')); // Return user data for editing
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Unique email, excluding the current user
            'phone' => 'required|max:15',
            'password' => 'nullable|string|min:6|confirmed', // Optional password update
        ]);

        // Find the user by ID, if not found, return a JSON error response
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!',
            ], 404);
        }

        // Update only the fields that are provided
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Only update the password if the user provided one
        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        // Save the updated user data to the database
        $user->save();

        // Return a success response with the updated user data
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully!',
            'user' => $user, // You can return the updated user data if needed
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found!'], 404);
        }

        $user->delete();
        return response()->json(['status' => true, 'message' => 'User deleted successfully!']);
    }

}
