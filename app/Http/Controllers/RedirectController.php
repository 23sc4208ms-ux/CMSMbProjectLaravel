<?php

namespace App\Http\Controllers;

class RedirectController extends Controller
{
    public function index()
    {
        return 'This is an index function of RedirectController';
    }

    public function showMessage(string $message)
    {
        return $message;
    }

    public function showSomething(string $message = 'something')
    {
        return $message;
    }
}
