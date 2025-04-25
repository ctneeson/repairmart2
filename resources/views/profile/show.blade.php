<x-app-layout title="View User">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="container-small">
        <h1 class="mb-0">User Profile</h1>
        <div class="card p-large mb-large">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-8">
                        <div class="row">
                            <div class="col">
                                <h2 class="mb-small">{{ $user->name }}</h2>
                            </div>
                            <div class="col">
                            </div>
                            <div class="col">
                            </div>
                            <div class="col">
                                @if(auth()->user()->id !== $user->id)
                                <a href="{{ route('email.create', ['recipient_ids' => [$user->id]]) }}"
                                    class="listing-details-email btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                       <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1
                                        0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                       <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    Message User
                                 </a>
                                 @endif
                            </div>
                        </div>
                        
                        <div class="quote-details">
                            <div class="row mb-4">
                            </div>
                        </div>
                        <div class="quote-details">
                            <div class="row mb-4">
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <label>Member since</label>
                                        <div class="detail-value">
                                        {{ $user->created_at->format('F j, Y') }}
                                        <span class="text-sm text-gray-500">({{ $user->created_at->diffForHumans() }})</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <label>Roles</label>
                                        <div class="detail-value">
                                            @foreach($user->roles as $role)
                                            > {{ ucfirst($role->name) }} <br>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <label>Location</label>
                                        <div class="detail-value">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493
                                                    31.493 0 0 1 8 14.58a31.481 31.481 0 0
                                                    1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3
                                                    6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8
                                                    16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3
                                                    3 0 0 0 0 6z"/>
                                            </svg>
                                            <span class="font-semibold">
                                                {{ $user->city }}, {{ $user->country->name ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <label>Feedback Summary</label>
                                        <table class="table table-striped table-bordered mt-2">
                                            <tbody>
                                                @foreach($feedbackTypes as $type)
                                                    <tr>
                                                        <td class="badge feedback-rating-{{ strtolower(str_replace(' ', '-', $type->name)) }}" 
                                                            style="width: 50%">
                                                            {{ $type->name }}
                                                        </td>
                                                        <td>{{ $feedbackCounts[$type->id]['count'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <h4 class="font-semibold text-lg mb-3">Activity Summary</h4>
                            <div class="row mb-4">
                                @if($isCustomer || $isSpecialist)
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <table class="table table-striped table-bordered mt-2">
                                            <tbody>
                                                @if ($isCustomer)
                                                <tr>
                                                    <td>Open Listings</td>
                                                    <td>{{ $listingCount }}</td>
                                                    @if($listingCount > 0)
                                                    <td>
                                                        <a href="{{ route('listings.search', ['user_id' => $user->id]) }}" 
                                                        class="text-sm text-blue-600 hover:underline">
                                                        View
                                                        </a>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endif
                                                @if ($isSpecialist)
                                                <tr>
                                                    <td>Open Quotes</td>
                                                    <td>{{ $quoteCount }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                                
                                @if($isCustomer || $isSpecialist)
                                <div class="col md-6">
                                    <div class="detail-group">
                                        <table class="table table-striped table-bordered mt-2">
                                            <tbody>
                                                @if($isCustomer)
                                                <tr>
                                                    <td>Orders Placed</td>
                                                    <td>{{ $customerOrderCount }}</td>
                                                </tr>
                                                @endif
                                                @if($isSpecialist)
                                                <tr>
                                                    <td>Orders Handled</td>
                                                    <td>{{ $specialistOrderCount }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            
                                @if(!$isCustomer && !$isSpecialist)
                                    <p class="text-gray-500 italic">No activity information to display.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back button -->
                    <div class="mt-6">
                        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900">
                            <i class="fa fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>