<x-guest-layout title="Login" bodyClass="page-login" :socialAuth="false">
    <h1 class="auth-page-title">Reset Password</h1>
    <form action="{{ route('password.reset-email') }}" method="post">
      @csrf

      <div class="form-group @error('email') has-error @enderror">
        <input type="email" placeholder="Your Email" name="email" value="{{ old('email') }}" />
        <div class="error-message">
          {{ $errors->first('email') }}
        </div>
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