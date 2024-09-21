<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\app\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        return view('user.index', compact('user'));
    }

    public function edit(User $user)
    {
        $currentUserId = Auth::id();

        if  ($user->id !== $currentUserId) {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }

        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kana' => ['required', 'string', 'regex:/\A[ァ-ヴー\s]+\z/u', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'postal_code' => ['required', 'digits:7'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'digits_between:10, 11'],
            'birthday' => ['nullable', 'digits:8'],
            'occupation' => ['nullable', 'string', 'max:255'],
        ]);

        $currentUserId = auth()->id();

        if  ($user->id !== $currentUserId) {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }

        $user->update($validated);

        return redirect()->route('user.index')->with('flash_message', '会員情報を編集しました。');

    }

}
