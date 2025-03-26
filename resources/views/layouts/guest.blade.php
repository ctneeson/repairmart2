@props(['title' => '', 'bodyClass' => ''])

<x-base-layout :title='$title' :bodyClass='$bodyClass'>

    @session('success')
    <div class="container my-large">
        <div class="success-message">
            {{ session('success') }}
        </div>
    </div>
    @endsession

    <main>
        <div class="container-small page-login">
            <div class="flex" style="gap: 5rem">
                <div class="auth-page-form">
                    <div class="text-center">
                        <a href="/">
                            <img src="/img/RepairMart-logo.png" alt="" />
                        </a>
                    </div>
                    {{$slot}}
                    <div class="grid grid-cols-2 gap-1 social-auth-buttons">
                        <x-google-button />
                        <x-facebook-button />
                    </div>
                    <div class="login-text-dont-have-account">
                        {{$footerLink}}
                    </div>
                </div>
                <div class="auth-page-image">
                    <img src="/img/electronics-repair.png" alt="" class="img-responsive" />
                </div>
            </div>
        </div>
    </main>
</x-base-layout>