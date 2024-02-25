<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function delete(Request $request)
    {

        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'Account deleted successfuly'], 200);
    }
}
