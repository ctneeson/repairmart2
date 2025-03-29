<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:45', 'regex:/^[0-9\+\-\(\)\s]+$/', 'unique:users,phone,' . $user->id],
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:50',
            'country_id' => 'required|exists:countries,id',
            'role' => 'required|array|min:1',
            'role.*' => 'in:customer,specialist',
        ];

        if (!$user->isOauthUser()) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $messages = [
            'required' => 'Please enter a value',
            'country_id.required' => 'Please select a country',
            'phone.regex' => 'Phone number may only contain numbers, spaces, and the following characters: +, -, (, )',
            'role.required' => 'Please select at least one role (Customer or Repair Specialist)',
            'role.min' => 'Please select at least one role (Customer or Repair Specialist)',
        ];

        $data = request()->validate($rules, $messages);

        // Remove role data from user update array
        $roleData = $data['role'];
        unset($data['role']);

        // Update user data
        $user->fill($data);

        // Sync roles
        // 1. Get the role IDs from the database based on submitted role names
        $roleIds = Role::whereIn('name', $roleData)->pluck('id')->toArray();

        // 2. Sync the roles (this will add new ones and remove old ones not in the array)
        $user->roles()->sync($roleIds);

        $successMessage = 'Profile updated successfully.';

        // If email is changed, user must re-validate it
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
            // Send verification email
            $user->sendEmailVerificationNotification();
            $successMessage = 'Profile updated successfully. Please verify your new email address.';
        }

        // Save the user
        $user->save();

        return redirect()->route('profile.index')->with('success', $successMessage);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = request()->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->delete();

        Auth::logout();

        return redirect()->route('home')->with('success', 'Account deleted successfully.');
    }
}
