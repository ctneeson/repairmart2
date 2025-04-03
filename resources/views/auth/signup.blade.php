<x-guest-layout title="Signup" bodyClass="page-signup">
  <h1 class="auth-page-title">Signup</h1>
    
  <form action="{{ route('signup.store') }}" method="post">
    @csrf
    <div class="form-group @error('name') has-error @enderror">
        <input type="text" placeholder="Your Name (customer or business)" name="name"
               value="{{ old('name') }}" />
        <div class="error-message">
          {{ $errors->first('name') }}
        </div>
      </div>
      <div class="form-group @error('email') has-error @enderror">
        <input type="email" placeholder="Your Email" name="email"
               value="{{ old('email') }}" />
        <div class="error-message">
          {{ $errors->first('email') }}
        </div>
      </div>
      <div class="form-group @error('password') has-error @enderror">
        <input type="password" placeholder="Your Password" name="password" />
        <div class="error-message">
          {{ $errors->first('password') }}
        </div>
      </div>
      <div class="form-group">
        <input type="password" placeholder="Repeat Password" name="password_confirmation" />
      </div>
      <div class="form-group @error('role') has-error @enderror">
        <label class="block mb-2">Register as:</label>
        <div class="checkbox-items">
          <div class="checkbox-item" style="width: 100%;">
            <input type="checkbox" id="customer-checkbox" name="role[]" value="customer" 
              {{ old('role') && in_array('customer', old('role')) ? 'checked' : '' }}>
            <label for="customer-checkbox">Customer</label>
          </div>
          <div class="checkbox-item" style="width: 100%;">
            <input type="checkbox" id="specialist-checkbox" name="role[]" value="specialist" 
              {{ old('role') && in_array('specialist', old('role')) ? 'checked' : '' }}>
            <label for="specialist-checkbox">Repair Specialist</label>
          </div>
        </div>
        <div class="error-message">
          {{ $errors->first('role') }}
        </div>
      </div>
      <hr />
      <div class="form-group @error('address_line1') has-error @enderror">
        <input type="text" placeholder="Address Line 1" name="address_line1"
               value="{{ old('address_line1') }}" />
        <div class="error-message">
          {{ $errors->first('address_line1') }}
        </div>
      </div>
      <div class="form-group @error('address_line2') has-error @enderror">
        <input type="text" placeholder="Address Line 2" name="address_line2"
               value="{{ old('address_line2') }}" />
        <div class="error-message">
          {{ $errors->first('address_line2') }}
        </div>
      </div>
      <div class="form-group @error('city') has-error @enderror">
        <input type="text" placeholder="Town/City" name="city"
               value="{{ old('city') }}" />
        <div class="error-message">
          {{ $errors->first('city') }}
        </div>
      </div>
      <div class="form-group @error('postcode') has-error @enderror">
        <input type="text" placeholder="Postcode" name="postcode"
               value="{{ old('postcode') }}" />
        <div class="error-message">
          {{ $errors->first('postcode') }}
        </div>
      </div>
      <div class="form-group @error('country_id') has-error @enderror">
        <x-select-country-all id="countrySelect" :value="old('country_id')" />
        <p class="error-message">{{ $errors->first('country_id') }}</p>
      </div>
      <div class="form-group @error('phone') has-error @enderror">
        <input type="text" placeholder="Phone" name="phone"
               value="{{ old('phone') }}" />
        <div class="error-message">
          {{ $errors->first('phone') }}
        </div>
      </div>
      <button class="btn btn-primary btn-login w-full">Register</button>
    </form>
    <x-slot:footerLink>
      Already have an account? -
      <a href="{{ route('login') }}"> Click here to log in </a>
    </x-slot:footerLink>
</x-guest-layout>