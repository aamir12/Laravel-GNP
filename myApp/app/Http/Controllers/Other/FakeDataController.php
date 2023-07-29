<?php

namespace App\Http\Controllers\Other;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Competition;
use Artisan;
use App\Http\Controllers\Controller;

/**
 * @hideFromAPIDocumentation 
 */
class FakeDataController extends Controller
{
    function userAndCompetition(Request $req)
    {
        Artisan::call('db:seed');
    }
}
