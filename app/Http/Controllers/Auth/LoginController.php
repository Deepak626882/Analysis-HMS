<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Companyreg;
use App\Models\EnviroGeneral;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    public function login(Request $request)
    {

        $credentials = $request->validate([
            'u_name' => 'required',
            'propertyid' => 'required',
            'password' => 'required'
        ]);

        // $companydcode = Companyreg::where('propertyid', $credentials['propertyid'])->orderBy('comp_code', 'DESC')->first();

        $userWithEmail = User::leftJoin('company', 'company.propertyid', '=', 'users.propertyid')
            ->where(function ($query) use ($credentials) {
                $query->where('users.u_name', $credentials['u_name'])
                    ->orWhere('users.email', $credentials['u_name']);
            })
            ->where('users.propertyid', $credentials['propertyid'])
            ->orderBy('company.comp_code', 'DESC')
            ->first();



        if (!$userWithEmail) {
            return back()->withErrors(['u_name' => 'Account does not exist'])->withInput();
        }


        $envgeneral = EnviroGeneral::where('propertyid', $userWithEmail->propertyid)->first();

        if ($envgeneral && $envgeneral->expdate && $envgeneral->propertyid != 103) {
            $expdate = Crypt::decryptString($envgeneral->expdate); 
            $ncurdate = $envgeneral->ncur; 

            if ($expdate < $ncurdate) {
                return back()->withErrors(['u_name' => 'Your account is expired. Please contact your software vendor.']);
            }
        }

        // exit;

        // if ($userWithEmail->status != 1) {
        //     return back()->withErrors(['u_name' => 'Account is not active'])->withInput();
        // }

        if (Auth::attempt($credentials)) {
            $user = Auth::user()->role;

            switch ($user) {
                case 1:
                    return redirect('/superadmin');
                    break;
                case 2:
                    return redirect('/company');
                    break;
                case 3:
                    return redirect('/user');
                    break;
                case 4:
                    return redirect('/staff');
                    break;
                case 5:
                    return redirect('/frontlogin');
                    break;
                default:
                    Auth::logout();
                    return back()->with('u_name', 'Oops Something went wrong');
            }
        } else {
            return back()->withErrors(['password' => 'Invalid password'])->withInput();
        }
    }
}
