<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index() {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }
}
