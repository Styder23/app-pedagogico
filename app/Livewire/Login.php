<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $showPassword = false;
    public $error = '';
    public $loading = false;

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login()
    {
        $this->error = '';
        $this->loading = true;

        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], true)) {
            session()->regenerate();
            $this->loading = false;

            return redirect()->intended(route('dashboard'));
        }

        $this->loading = false;
        $this->error = 'Credenciales incorrectas';
    }

    public function mount()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.login')->layout('layouts.app');
    }
}
