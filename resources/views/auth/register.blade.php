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
        <div class="container container-register">
            <x-slot name="logo">
                
            </x-slot>

            <h3 class="ttl">会員登録</h3>
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('register') }}" class="form-body">
                @csrf

                <!-- Name -->
                <div>

                    <x-input id="name" class="block mt-1 w-full border" type="text" name="name" placeholder="名前" :value="old('name')" required autofocus />
                </div>

                <!-- Email Address -->
                <div class="mt-4">

                    <x-input id="email" class="block mt-1 w-full border" type="email" name="email" placeholder="メールアドレス" :value="old('email')" required />
                </div>

                <!-- Password -->
                <div class="mt-4">

                    <x-input id="password" class="block mt-1 w-full border"
                                    type="password"
                                    name="password"
                                    placeholder="パスワード"
                                    required autocomplete="new-password" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">

                    <x-input id="password_confirmation" class="block mt-1 w-full border"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="確認用パスワード"
                                    required />
                </div>


                <div class="mt-30">
                    

                    <button class="btn">
                        {{ __('会員登録') }}
                    </button>
                </div>
                
            </form>
            <div class="navigation">
                <p>アカウントをお持ちの方はこちらから</p>
                <a  href="{{ route('login') }}">
                    {{ __('ログイン') }}
                </a>
            </div>
            
            
        <div>
    </main>
    <footer class="footer">
        <small class="footer-logo">Atte,inc.</small>
    </footer>
</x-guest-layout>
