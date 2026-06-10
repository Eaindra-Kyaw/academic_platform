<x-guest-layout>
    @php
        $role = request()->get('role');
        $roleTitle = '';
        $demoEmail = '';
        $demoPassword = '';

        if ($role == 'admin') {
            $roleTitle = 'Administrator';
            $demoEmail = 'admin1@mtu.edu.mm';
            $demoPassword = 'admin001';
        } elseif ($role == 'lecturer') {
            $roleTitle = 'Lecturer';
            $demoEmail = 'phyothuzartun@mtu.edu.mm';
            $demoPassword = 'phyo123';
        } elseif ($role == 'student') {
            $roleTitle = 'Student';
            $demoEmail = 'eaindrakyaw@mtu.edu.mm';
            $demoPassword = 'eain123';
        }
    @endphp

    <div style="max-width: 500px; margin: 0 auto; text-align: center;">
        <div
            style="display: inline-flex; align-items: center; gap: 15px; background: rgba(128,0,0,0.1); padding: 12px 30px; border-radius: 60px; margin-bottom: 30px;">
            <div
                style="width: 50px; height: 50px; background: #FFD700; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #800000; font-weight: bold; font-size: 22px;">
                MTU</div>
            <div>
                <div style="font-weight: 700; font-size: 18px; color: #800000;">Mandalay Technological University</div>
                <div style="font-size: 12px; color: #666;">Academic Intelligence System</div>
            </div>
        </div>

        @if ($roleTitle)
            <div style="background: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 12px; margin-bottom: 20px;">
                <i class="bi bi-info-circle"></i> Logging in as <strong>{{ $roleTitle }}</strong>
            </div>
        @endif

        <h2 style="font-size: 28px; font-weight: 700; color: #800000; margin-bottom: 10px;">Welcome Back</h2>
        <p style="color: #666; margin-bottom: 30px;">Sign in to your account</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $demoEmail)"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" :value="$demoPassword"
                    required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button style="background: #800000;">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>

        <div class="demo-credentials"
            style="margin-top: 30px; padding: 15px; background: #fef9c3; border-radius: 16px;">
            <p style="font-size: 12px; color: #854d0e; margin-bottom: 8px;"><i class="bi bi-info-circle"></i> Demo
                Credentials</p>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; font-size: 11px;">
                <span style="background: white; padding: 4px 10px; border-radius: 20px;">👑 Admin: admin1@mtu.edu.mm /
                    admin001</span>
                <span style="background: white; padding: 4px 10px; border-radius: 20px;">👨‍🏫 Lecturer:
                    phyothuzartun@mtu.edu.mm / phyo123</span>
                <span style="background: white; padding: 4px 10px; border-radius: 20px;">🎓 Student:
                    eaindrakyaw@mtu.edu.mm / eain123</span>
            </div>
        </div>
    </div>
</x-guest-layout>
