<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class StudentLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::guard('student')->attempt($credentials, $this->remember)) {
            session()->regenerate();
            return redirect()->intended('/stpage');
        }

        $this->errorMessage = 'Invalid email or password';
    }

    public function render()
    {
        return view('livewire.student-login');
    }
}
