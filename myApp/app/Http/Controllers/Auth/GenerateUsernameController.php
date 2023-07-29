<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;


class GenerateUsernameController extends Controller
{
    public function generateUsername()
    {
        $data = ['username' => UsernameGenerator::generate()];
        return response()->success(__('auth')['generate_username'], $data);
    }
}