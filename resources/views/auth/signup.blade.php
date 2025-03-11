<x-guest-layout title="Signup" bodyClass="page-signup">
  <h1 class="auth-page-title">Signup</h1>
    
  <form action="" method="post">
      <div class="form-group">
        <input type="email" placeholder="Your Email" />
      </div>
      <div class="form-group">
        <input type="password" placeholder="Your Password" />
      </div>
      <div class="form-group">
        <input type="password" placeholder="Repeat Password" />
      </div>
      <hr />
      <div class="form-group">
        <input type="text" placeholder="Name" />
      </div>
      <div class="form-group">
        <input type="text" placeholder="Address Line 1" />
      </div>
      <div class="form-group">
        <input type="text" placeholder="Address Line 2" />
      </div>
      <div class="form-group">
        <input type="text" placeholder="Town/City" />
      </div>
      <div class="form-group">
        <input type="text" placeholder="Postcode" />
      </div>
      <div class="form-group">
        <select>
          <option value="">Country</option>
          <option value="GB">United Kingdom</option>
          <option value="IE">Ireland</option>
          <option value="FR">France</option>
          <option value="NL">Netherlands</option>
          <option value="BE">Belgium</option>
        </select>
      </div>
      <div class="form-group">
        <input type="text" placeholder="Phone" />
      </div>
      <button class="btn btn-primary btn-login w-full">Register</button>
    </form>
    <x-slot:footerLink>
      Already have an account? -
      <a href="/login.html"> Click here to log in </a>
    </x-slot:footerLink>
</x-guest-layout>