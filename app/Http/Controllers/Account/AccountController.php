<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $user      = Auth::user();
        $orders    = $user->orders()->with('items')->paginate(10);
        $addresses = $user->addresses()->get();

        return view('account.index', [
            'title'     => 'My Account',
            'user'      => $user,
            'orders'    => $orders,
            'addresses' => $addresses,
        ]);
    }
}
