<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('account.index', ['title' => 'My Account']);
    }
}
