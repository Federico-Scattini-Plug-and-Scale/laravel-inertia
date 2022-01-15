<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AuthDataController extends Controller
{
    public function index(User $user)
    {
        return Inertia::render('Company/Auth/AuthData', [
			'company' => $user
		]);    
	}

    public function changePassword(User $user, Request $request)
    {
        $request->validate([
			'old_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'different:old_password', Password::defaults()],
        ]);
		
		if (Hash::check($request->old_password, $user->password))
		{
			$user->update([
				'password' => Hash::make($request->new_password),
			]);
	
			return redirect()->back()->with('message', [
				'type' => 'success',
				'content' => __('The password has been successfully changed.')
			]);
		}

		return redirect()->back()->with('message', [
            'type' => 'error',
            'content' => __('The password does not match.')
        ]);
    }

	public function changeEmail(User $user, Request $request)
    {
		if ($request->email == $user->email)
		{
			return redirect()->back()->with('message', [
				'type' => 'error',
				'content' => __('Please provide a different email from the previous one.')
			]);
		}

        $request->validate([
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
        ]);

		$user->update([
			'email' => $request->email,
			'email_verified_at' => null
		]);

		$user->sendEmailVerificationNotification();

		return redirect()->back();
    }
}
