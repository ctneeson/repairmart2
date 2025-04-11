<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Basic User Information -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-4">{{ $user->name }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">
                                    <span class="font-semibold">Location:</span> 
                                    {{ $user->city }}, {{ $user->country->name ?? 'Unknown' }}
                                </p>
                                
                                <p class="text-gray-600">
                                    <span class="font-semibold">Member since:</span>
                                    {{ $user->created_at->format('F j, Y') }}
                                    <span class="text-sm text-gray-500">({{ $user->created_at->diffForHumans() }})</span>
                                </p>
                                
                                <div class="mt-4">
                                    <span class="font-semibold text-gray-600">Roles:</span>
                                    <div class="mt-1 flex flex-wrap gap-2">
                                        @foreach($user->roles as $role)
                                            <span class="px-3 py-1 rounded-full text-sm 
                                                @if($role->name == 'customer') bg-blue-100 text-blue-800
                                                @elseif($role->name == 'specialist') bg-green-100 text-green-800
                                                @elseif($role->name == 'admin') bg-purple-100 text-purple-800
                                                @endif">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-lg mb-3">Activity Summary</h4>
                                
                                @if($isCustomer)
                                    <div class="mb-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Repair Listings Created:</span>
                                            <span class="font-semibold">{{ $listingCount }}</span>
                                        </div>
                                        @if($listingCount > 0)
                                            <a href="{{ route('listings.search', ['user_id' => $user->id]) }}" 
                                               class="text-sm text-blue-600 hover:underline">
                                                View Listings
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Orders Placed as Customer:</span>
                                            <span class="font-semibold">{{ $customerOrderCount }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($isSpecialist)
                                    <div class="mb-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Repairs Completed as Specialist:</span>
                                            <span class="font-semibold">{{ $specialistOrderCount }}</span>
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