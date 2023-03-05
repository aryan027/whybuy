<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Validator;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        // $validated = $request->validateWithBag('updatePassword', [
        //     'current_password' => ['required', 'current_password'],
        //     // 'password' => ['required', Password::defaults(), 'confirmed'],
        //     // 'password_confirmation' => ['required','current_password'],
        // ]);

    
        $rule = [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required','same:password'],
        ];
        $valid = Validator::make($request->all(),$rule);
        if($valid->fails()){
            return redirect()->back()->withErrors($valid->errors())->withInput();
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }
}
