<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        $existingUser = User::where('mobile', $user->mobile)->first();

        if ($existingUser) {
           return $this->SuccessResponse(200,'fetch');
        } else {
            $newUser = new User;
            $newUser->mobile = $user->mobile;
            $newUser->google_id = $user->id;
            $newUser->save();

            return $this->SuccessResponse(200,'fetch');
        }

        return $this->SuccessResponse(200,'fetch');
    }

    public function disconnectGoogle(Request $request)
    {
        $user = $request->user();
        $user->google_id = null;
        $user->save();

        return redirect()->back();
    }

}
