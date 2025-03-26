<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SignupController extends Controller
{
    public function create()
    {
        return view('auth.signup');
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => 'Please enter a value',
            'country_id.required' => 'Please select a country',
            'phone.regex' => 'Phone number may only contain numbers, spaces, and the following characters: +, -, (, )',
            'role.required' => 'Please select at least one role (Customer or Repair Specialist)',
            'role.min' => 'Please select at least one role (Customer or Repair Specialist)',
        ];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['nullable', 'string', 'max:45', 'regex:/^[0-9\+\-\(\)\s]+$/', 'unique:users,phone'],
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
            'google_id' => 'nullable|string|max:45',
            'facebook_id' => 'nullable|string|max:45',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:50',
            'country_id' => 'required|exists:countries,id',
            'role' => 'required|array|min:1', // At least one role required
            'role.*' => 'in:customer,specialist', // Validate each selected role    
        ], $messages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'google_id' => $request->google_id,
            'facebook_id' => $request->facebook_id,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'country_id' => $request->country_id,
        ]);

        // Assign roles
        $selectedRoles = Role::whereIn('name', $request->role)->get();
        $user->roles()->attach($selectedRoles);


        return redirect()->route('login')
            ->with('success', 'Account created successfully.');
    }
}
