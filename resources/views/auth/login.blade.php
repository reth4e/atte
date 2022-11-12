<x-guest-layout>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Atte</title>
        <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
        <link rel="stylesheet" href="{{ asset('css/certification.css') }}">
    </head>
    <header class="header">
        <h2 class="header-title">
            Atte
        </h2>
    </header>
    <main>
        <div class="container container-login">
            <x-slot name="logo">
                
            </x-slot>

            <h3 class="ttl">ログイン</h3>
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('login') }}" class="form-body">
                @csrf

                <!-- Email Address -->
                <div>

                    <x-input id="email" class="block mt-1 w-full border" type="email" name="email" placeholder="メールアドレス" :value="old('email')" required autofocus />
                </div>

                <!-- Password -->
                <div class="mt-30">

                    <x-input id="password" class="block mt-1 w-full border"
                                    type="password"
                                    name="password"
                                    placeholder="パスワード"
                                    required autocomplete="current-password" />
                </div>

                

                <div class=" mt-30">
                    

                    <button class=" btn">
                        {{ __('ログイン') }}
                    </button>
                </div>
            </form>
            <div class="navigation">
                <p>アカウントをお持ちでない方はこちらから</p>
                <a href="{{ route('register') }}">{{ __('会員登録') }}</a>
            </div>
        </div>
    </main>
    <footer class="footer">
        <small class="footer-logo">Atte,inc.</small>
    </footer>
</x-guest-layout>
