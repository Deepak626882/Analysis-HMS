<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLoginController extends Controller
{
    public function loginUser()
    {
        $userid = request('userid');
        $username = request('username');
        $propertyid = request('propertyid');
        $redirectUrl = request('redirect_url', url('/dashboard'));

        $chkuser = User::where('propertyid', $propertyid)->where('u_name', $username)->first();

        // return $chkuser;

        if (is_null($chkuser)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not permit for this propertyid'
            ]);
        }

        if (Auth::loginUsingId($userid)) {
            return response()->json([
                'status' => 'success',
                'redirect' => $redirectUrl
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found.'
        ]);
    }
}
