<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Moder\User;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function provider(){
        return Socialite::driver('linkedin')->stateless()->user();
    }

    public function providerCallback(Request $request){
        $linkedin = Socialite::driver('linkedin')->user();
        echo 'sadsadas';
        dd($linkedin);
        
    }
}