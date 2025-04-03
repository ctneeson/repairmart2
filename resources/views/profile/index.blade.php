<x-app-layout>
    <main>
        <div class="container-small">
            <h1 class="listing-details-page-title">My Account</h1>
            <form action="{{ route('profile.update') }}" method="POST"
                class="card p-large my-large">
                @csrf
                @method('PUT')

                <div class="form-group @error('name') has-error @enderror">
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Name"
                        value="{{ old('name', $user->name) }}" required>
                    <p class="error-message">{{ $errors->first('name') }}</p>
                </div>
                <div class="form-group @error('email') has-error @enderror">
                    <label>Email Address</label>
                    <input type="text" name="email" placeholder="Email"
                        value="{{ old('email', $user->email) }}" required
                        @disabled($user->isOauthUser())>
                    <p class="error-message">{{ $errors->first('email') }}</p>
                </div>
                <div class="form-group @error('role') has-error @enderror">
                    <label class="block mb-2">Role(s):</label>
                    <div class="checkbox-items">
                      <div class="checkbox-item" style="width: 100%;">
                        <input type="checkbox" id="customer-checkbox" name="role[]" value="customer" 
                        {{ (old('role') && in_array('customer', old('role'))) || 
                           (empty(old('role')) && $user->roles->contains('name', 'customer')) ? 'checked' : '' }}>                    
                        <label for="customer-checkbox">Customer</label>
                      </div>
                      <div class="checkbox-item" style="width: 100%;">
                        <input type="checkbox" id="specialist-checkbox" name="role[]" value="specialist" 
                        {{ (old('role') && in_array('specialist', old('role'))) || 
                           (empty(old('role')) && $user->roles->contains('name', 'specialist')) ? 'checked' : '' }}>                    
                        <label for="specialist-checkbox">Repair Specialist</label>
                      </div>
                    </div>
                    <div class="error-message">
                      {{ $errors->first('role') }}
                    </div>
                </div>
                <hr />
                <div class="form-group @error('address_line1') has-error @enderror">
                    <label>Address Line 1</label>
                    <input type="text" placeholder="Address Line 1" name="address_line1"
                           value="{{ old('address_line1', $user->address_line1) }}" required/>
                    <div class="error-message">
                        {{ $errors->first('address_line1') }}
                    </div>
                </div>
                <div class="form-group @error('address_line2') has-error @enderror">
                    <label>Address Line 2</label>
                    <input type="text" placeholder="Address Line 2" name="address_line2"
                           value="{{ old('address_line2', $user->address_line2) }}" />
                    <div class="error-message">
                        {{ $errors->first('address_line2') }}
                    </div>
                </div>
                <div class="form-group @error('city') has-error @enderror">
                    <label>City</label>
                    <input type="text" placeholder="Town/City" name="city"
                           value="{{ old('city', $user->city) }}" required/>
                    <div class="error-message">
                      {{ $errors->first('city') }}
                    </div>
                </div>
                <div class="form-group @error('postcode') has-error @enderror">
                    <label>Postcode</label>
                    <input type="text" placeholder="Postcode" name="postcode"
                           value="{{ old('postcode', $user->postcode) }}" required/>
                    <div class="error-message">
                      {{ $errors->first('postcode') }}
                    </div>
                </div>
                <div class="form-group @error('country_id') has-error @enderror">
                    <label>Country</label>
                    <x-select-country-all id="countrySelect" :value="old('country_id', $user->country_id)" />
                    <p class="error-message">{{ $errors->first('country_id') }}</p>
                </div>
                <div class="form-group @error('phone') has-error @enderror">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Phone Number"
                        value="{{ old('phone', $user->phone) }}">
                    <p class="error-message">{{ $errors->first('phone') }}</p>
                </div>
                <div class="p-medium">
                    <div class="flex justify-end gap-1">
                        <button type="reset" class="btn btn-default">Reset</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
            <form action="{{ route('profile.updatePassword') }}" method="POST"
                class="card p-large my-large">
                @csrf
                @method('PUT')

                <div class="form-group @error('current_password') has-error @enderror">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Current Password">
                    <p class="error-message">{{ $errors->first('current_password') }}</p>
                </div>
                <div class="form-group @error('password') has-error @enderror">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="New Password">
                    <p class="error-message">{{ $errors->first('password') }}</p>
                </div>
                <div class="form-group">
                    <label>Re-enter New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Re-enter New Password">
                </div>
                <div class="p-medium">
                    <div class="flex justify-end gap-1">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</x-app-layout>
