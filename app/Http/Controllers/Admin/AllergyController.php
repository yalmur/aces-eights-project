<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AllergyController extends Controller
{
    public function index(): View
    {
        return view('admin.allergy.index', ['title' => 'Allergy Management']);
    }
}
