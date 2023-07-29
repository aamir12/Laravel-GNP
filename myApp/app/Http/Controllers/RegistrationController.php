<?php

namespace App\Http\Controllers;

use App\Models\Branding;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $req)
    {
        return view('frontend/register', [
            'email' => $req->query('email'),
            'activation_code' => $req->query('activation_code'),
            'branding' => Branding::first(),
        ]);
    }
}
