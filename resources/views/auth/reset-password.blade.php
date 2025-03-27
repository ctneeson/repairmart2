<x-guest-layout title="Login" bodyClass="page-login" :socialAuth="false">
    <h1 class="auth-page-title">Reset Password</h1>
    <form action="{{ route('password.update', $token) }}" method="post">
      @csrf

      <input type="hidden" name="token" value="{{ request('token') }}">

      <div class="form-group @error('email') has-error @enderror">
        <input type="email" readonly name="email" value="{{ request('email') }}" />
        <div class="error-message">
          {{ $errors->first('email') }}
        </div>
      </div>

      <div class="form-group @error('password') has-error @enderror">
        <input type="password" placeholder="New Password" name="password" />
        <div class="error-message">
          {{ $errors->first('password') }}
        </div>
      </div>

      <div class="form-group">
        <input type="password" placeholder="Confirm New Password" name="password_confirmation" />
      </div>

      <button class="btn btn-primary btn-login w-full">
        Submit
      </button>

      <div class="login-text-dont-have-account">
        Already have an account? -
        <a href="{{ route('login') }}"> Log in here </a>
      </div>
  
    </form>
</x-guest-layout>