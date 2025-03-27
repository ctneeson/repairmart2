@props(['title' => '', 'bodyClass' => '', 'socialAuth' => true])

<x-base-layout :title='$title' :bodyClass='$bodyClass'>

    <main>
        <div class="container-small page-login">
            <div class="flex" style="gap: 5rem">
                <div class="auth-page-form">
                    <div class="text-center">
                        <a href="/">
                            <img src="/img/RepairMart-logo.png" alt="" />
                        </a>
                    </div>

                    @session('success')
                    <div class="my-large">
                        <div class="success-message">
                            {{ session('success') }}
                        </div>
                    </div>
                    @endsession
                
                    {{$slot}}
                    
                    @if($socialAuth)
                    <div class="grid grid-cols-2 gap-1 social-auth-buttons">
                        <x-google-button />
                        <x-facebook-button />
                    </div>
                    @endif

                    @isset($footerLink)
                    <div class="login-text-dont-have-account">
                        {{$footerLink}}
                    </div>
                    @endisset

                </div>
                <div class="auth-page-image">
                    <img src="/img/electronics-repair.png" alt="" class="img-responsive" />
                </div>
            </div>
        </div>
    </main>
</x-base-layout>