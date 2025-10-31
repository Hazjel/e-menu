@php
    $brand = config('app.name', 'Admin');
@endphp

<x-filament-panels::page.simple>
    <div class="fi-auth-card w-full max-w-[28rem] mx-auto">
        {{-- <div class="text-center mb-6">
            <a href="/" class="inline-flex items-center gap-2">
                <img src="{{ asset('favicon.ico') }}" alt="Logo" class="h-8 w-8">
                <span class="text-xl font-semibold">{{ $brand }}</span>
            </a>
        </div> --}}

        <x-filament-panels::form id="login" wire:submit="authenticate" class="space-y-6">
            {{ $this->form }}
            <div>
                <x-filament-panels::form.actions :actions="$this->getFormActions()" class="w-full justify-center" />
            </div>
        </x-filament-panels::form>

        @if (filament()->hasPasswordReset())
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <x-filament::link :href="filament()->getRequestPasswordResetUrl()" color="primary">
                    {{ __('auth.login.forgot') }}
                </x-filament::link>
            </div>
        @endif

        @if (\Illuminate\Support\Facades\Route::has('filament.admin.auth.register'))
            <div class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('auth.login.no_account') }}
                <x-filament::link :href="route('filament.admin.auth.register')" color="primary" tag="a" class="font-medium">
                    {{ __('auth.login.register') }}
                </x-filament::link>
            </div>
        @endif
    </div>
</x-filament-panels::page.simple>
