<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;

class Logout
{
    use Toast;
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
//        $this->warning("Logged out! Redirect you back to home.");
    }
}
