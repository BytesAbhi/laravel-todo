<?php

use App\Models\UserEmail;
use Illuminate\Http\Request;

public function store(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:user_emails,email',
    ]);

    UserEmail::create(['email' => $request->email]);

    return response()->json(['message' => 'Email saved']);
}
