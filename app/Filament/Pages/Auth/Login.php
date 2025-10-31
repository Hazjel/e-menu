<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Blade;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getLoginFormComponent(): TextInput
    {
        return TextInput::make('login')
            ->label(__('auth.login.email_or_username'))
            ->autocomplete('username')
            ->required();
    }

    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label(__('auth.login.password'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Checkbox
    {
        return Checkbox::make('remember')
            ->label(__('auth.login.remember'));
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'] ?? '';
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $data['password'] ?? '',
        ];
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        $primary = $actions[0] ?? null;
        if ($primary) {
            $primary
                ->label(__('auth.login.submit'))
                ->extraAttributes([
                    'class' => 'w-full justify-center',
                ]);

            // Kembalikan hanya aksi utama; link "Lupa kata sandi?" ditangani di Blade kustom
            return [$primary];
        }

        return [];
    }
}


