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
                        value="{{ old('email', $user->email) }}" required>
                    <p class="error-message">{{ $errors->first('email') }}</p>
                </div>
                <div class="form-group @error('phone') has-error @enderror">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Phone Number"
                        value="{{ old('phone', $user->phone) }}" required>
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
