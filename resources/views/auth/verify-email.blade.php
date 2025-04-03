<x-app-layout>
    <div class="container">
        <div class="card p-large my-large">
            <h2>Verify your Email Address</h2>
            <div class="my-medium">
                Before proceeding, please check your email for a verification link.
                If you did not receive the email,
                <form action="{{ route('verification.send') }}" method="post" class="inline-flex">
                    @csrf
                    <button type="submit" class="btn-link">cilck here to request another.</button>
                </form>
            </div>
        </div>

        <div>
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary">Log out</button>
            </form>
        </div>

    </div>
</x-app-layout>