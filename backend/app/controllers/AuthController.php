<?php

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth/signin', [], null);
    }

    public function register(): void
    {
        $this->view('auth/signup', [], null);
    }
}
