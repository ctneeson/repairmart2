<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @param User|null $user
     * @return \Illuminate\View\View
     */
    public function index(User $user = null)
    {
        $currentUser = Auth::user();

        // If no specific user is requested or the user is not an admin, show their own profile
        if ($user === null || !$currentUser->roles->contains('name', 'admin')) {
            return view('profile.index', ['user' => $currentUser]);
        }

        // If an admin is requesting another user's profile
        if ($currentUser->roles->contains('name', 'admin')) {
            // Optionally, you can pass a flag to the view to indicate we're in "admin edit mode"
            return view('profile.index', [
                'user' => $user,
                'isAdminEdit' => true
            ]);
        }

        // Fallback (this should never be reached but added for safety)
        return view('profile.index', ['user' => $currentUser]);
    }

    /**
     * Update the user's profile.
     *
     * @param Request $request
     * @param User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user = null)
    {
        // Determine which user to update
        $targetUser = $user ?? auth()->user();

        // Authorization check - only admins can update other users
        if ($targetUser->id !== auth()->id() && !auth()->user()->roles->contains('name', 'admin')) {
            abort(403, 'You do not have permission to update this profile.');
        }

        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:45', 'regex:/^[0-9\+\-\(\)\s]+$/', 'unique:users,phone,' . $targetUser->id],
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:50',
            'country_id' => 'required|exists:countries,id',
            'role' => 'required|array|min:1',
            'role.*' => 'in:customer,specialist,admin',
        ];

        // Only validate email if user is not an OAuth user
        if (!$targetUser->isOauthUser()) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $targetUser->id];
        }

        $messages = [
            'required' => 'Please enter a value',
            'country_id.required' => 'Please select a country',
            'phone.regex' => 'Phone number may only contain numbers, spaces, and the following characters: +, -, (, )',
            'role.required' => 'Please select at least one role (Customer or Repair Specialist)',
            'role.min' => 'Please select at least one role (Customer or Repair Specialist)',
        ];

        $validated = $request->validate($rules, $messages);

        // Extract role data before removing it from the attributes to update
        $selectedRoles = $validated['role'] ?? [];
        unset($validated['role']);

        // Update user basic attributes
        $targetUser->update($validated);

        // Update user roles - first retrieve existing admin status
        $wasAdmin = $targetUser->roles->contains('name', 'admin');

        // Get role IDs from names
        $roleIds = Role::whereIn('name', $selectedRoles)->pluck('id')->toArray();

        // If user was admin and is still admin according to form, make sure admin role is included
        if ($wasAdmin && auth()->user()->id === $targetUser->id) {
            $adminRoleId = Role::where('name', 'admin')->first()->id;
            if (!in_array($adminRoleId, $roleIds)) {
                $roleIds[] = $adminRoleId; // Prevent removing admin role from self
            }
        }

        // Only admins can modify admin role assignments
        if (in_array('admin', $selectedRoles) && !auth()->user()->roles->contains('name', 'admin')) {
            return redirect()->back()->with('error', 'You do not have permission to assign admin privileges.');
        }

        // Sync roles - this will add new roles and remove ones not in the array
        $targetUser->roles()->sync($roleIds);

        if ($targetUser->id !== auth()->id()) {
            // Admin updating someone else, redirect back to admin view
            return redirect()->route('profile.admin.index', ['user' => $targetUser->id])
                ->with('success', 'Profile updated successfully');
        }

        // User updating themselves, redirect to profile
        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully');
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

    /**
     * Delete the user account.
     *
     * @param User|null $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user = null)
    {
        dump($user);
        // When a user is deleting their own account (non-admin flow)
        if ($user === null) {
            $user = Auth::user();
            dd($user);
            $user->delete();

            Auth::logout();
            return redirect()->route('home')->with('success', 'Account deleted successfully.');
        }

        // Admin flow - deleting another user's account
        $currentUser = Auth::user();

        // Check if the current user is an admin
        if (!$currentUser->roles->contains('name', 'admin')) {
            return redirect()->back()->with('error', 'You do not have permission to delete other users.');
        }

        // Admin safety check - prevent self-deletion
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Proceed with deletion
        $userName = $user->name;
        $user->delete();

        // Redirect admin back to user search without logging them out
        return redirect()->route('profile.search')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }

    /**
     * Search for users based on filters.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        $usersQuery = User::with('roles')
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($builder) use ($query) {
                    $builder->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->orderBy('name');

        $users = $usersQuery->paginate(15)->withQueryString();

        return view('profile.search', compact('users'));
    }

}
