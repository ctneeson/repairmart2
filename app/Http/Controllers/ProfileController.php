<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\FeedbackType;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @param User|null $user
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(User $user = null)
    {
        $currentUser = Auth::user();

        // If no specific user is requested, show own profile
        if ($user === null) {
            return view('profile.index', ['user' => $currentUser]);
        }

        // Check authorization for viewing a specific user's profile
        $response = Gate::inspect('view', $user);

        if (!$response->allowed()) {
            return redirect()->route('profile.index')
                ->with('error', $response->message());
        }

        // If an admin is requesting another user's profile
        if ($currentUser->hasRole('admin')) {
            return view('profile.index', [
                'user' => $user,
                'isAdminEdit' => true
            ]);
        }

        // User viewing their own profile
        return view('profile.index', ['user' => $user]);
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

        // Authorization check
        $response = Gate::inspect('update', $targetUser);

        if (!$response->allowed()) {
            return redirect()->route('profile.index')
                ->with('error', $response->message());
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

        // Check permission to assign admin role
        if (in_array('admin', $selectedRoles)) {
            $adminResponse = Gate::inspect('assignAdminRole', $targetUser);

            if (!$adminResponse->allowed()) {
                return redirect()->back()
                    ->with('error', $adminResponse->message());
            }
        }

        // Sync roles - this will add new roles and remove ones not in the array
        $targetUser->roles()->sync($roleIds);

        if ($targetUser->id !== auth()->id()) {
            // Admin updating someone else, redirect back to admin view
            return redirect()->route('profile.index', ['user' => $targetUser->id])
                ->with('success', 'Profile updated successfully');
        }

        // User updating themselves, redirect to profile
        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Update the user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
        // Determine which user to delete
        $targetUser = $user ?? Auth::user();

        // Check permission to delete
        $response = Gate::inspect('delete', $targetUser);

        if (!$response->allowed()) {
            return redirect()->back()
                ->with('error', $response->message());
        }

        // Save user name for success message
        $userName = $targetUser->name;

        // Proceed with deletion
        $targetUser->delete();

        // If user deleted their own account, log them out
        if (Auth::id() === $targetUser->id) {
            Auth::logout();
            return redirect()->route('home')
                ->with('success', 'Account deleted successfully.');
        }

        // Admin deleting another user
        return redirect()->route('profile.search')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }

    /**
     * Search for users based on filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        // Check if user can view user listings (admin only)
        $response = Gate::inspect('viewAny', User::class);

        if (!$response->allowed()) {
            return redirect()->route('home')
                ->with('error', $response->message());
        }

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

    /**
     * Show public profile overview for a user.
     *
     * @param User $user
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        // Only show profiles of active users
        if (!$user->exists) {
            return redirect()->route('home')
                ->with('error', 'User not found.');
        }

        $isCustomer = $user->hasRole('customer');
        $isSpecialist = $user->hasRole('specialist');

        $listingCount = 0;
        $customerOrderCount = 0;
        $specialistOrderCount = 0;

        if ($isCustomer) {
            $listingCount = $user->listingsCreated()
                ->whereHas('status', function ($query) {
                    $query->where('name', 'Open');
                })
                ->count();
            $customerOrderCount = $user->customerOrders()->count();
        }

        if ($isSpecialist) {
            $quoteCount = $user->quotesCreated()
                ->whereHas('status', function ($query) {
                    $query->where('name', 'Open');
                })
                ->count();
            $specialistOrderCount = $user->repairSpecialistOrders()->count();
        }

        // Get all feedback types
        $feedbackTypes = FeedbackType::orderBy('id')->get();

        // Initialize the counts array
        $feedbackCounts = [];

        foreach ($feedbackTypes as $type) {
            $feedbackCounts[$type->id] = [
                'name' => $type->name,
                'count' => 0
            ];
        }

        // Get feedback received as a specialist (from customers)
        if ($isSpecialist) {
            $specialistFeedbackCounts = \DB::table('orders')
                ->join('feedback_types', 'orders.customer_feedback_id', '=', 'feedback_types.id')
                ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
                ->where('orders.specialist_id', $user->id) // Fix: This should be specialist_id, not customer_id
                ->where('orders.customer_feedback_id', '!=', null)
                ->where('order_statuses.name', '=', 'Closed')
                ->groupBy('feedback_types.id', 'feedback_types.name')
                ->select('feedback_types.id', 'feedback_types.name', \DB::raw('count(*) as count'))
                ->get();

            foreach ($specialistFeedbackCounts as $feedback) {
                if (isset($feedbackCounts[$feedback->id])) {
                    $feedbackCounts[$feedback->id]['count'] += $feedback->count;
                }
            }
        }

        // Get feedback received as a customer (from specialists)
        if ($isCustomer) {
            $customerFeedbackCounts = \DB::table('orders')
                ->join('feedback_types', 'orders.specialist_feedback_id', '=', 'feedback_types.id')
                ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
                ->where('orders.customer_id', $user->id)
                ->where('orders.specialist_feedback_id', '!=', null)
                ->where('order_statuses.name', '=', 'Closed')
                ->groupBy('feedback_types.id', 'feedback_types.name')
                ->select('feedback_types.id', 'feedback_types.name', \DB::raw('count(*) as count'))
                ->get();

            foreach ($customerFeedbackCounts as $feedback) {
                if (isset($feedbackCounts[$feedback->id])) {
                    $feedbackCounts[$feedback->id]['count'] += $feedback->count;
                }
            }
        }

        return view('profile.show', compact(
            'user',
            'isCustomer',
            'isSpecialist',
            'listingCount',
            'quoteCount',
            'customerOrderCount',
            'specialistOrderCount',
            'feedbackTypes',
            'feedbackCounts'
        ));
    }
}