<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser', ['only' => ['edit', 'show']]);
    }

    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'admin' => Auth::user()
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Profile', [
            'admin' => $user
        ]);
    }

    public function edit(User $user, Request $request)
    {
        $request->validate($this->validationRules());

        $user->update([
            'email' => $request->email
        ]);

        return redirect()->route('admin.profile', $user)->with('message', [
            'type' => 'success',
            'content' => __('The data has been modified successfully.')
        ]);
    }

    private function validationRules()
    {
        return [
            'email' => 'email|required',
        ];
    }
}
