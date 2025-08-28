<?php

namespace App\Providers;

use App\Models\Companyreg as Company;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('property.layouts.header', function ($view) {
            // $user = Auth::user();
            // if ($user) {
            //     $mail = $user->email;
            //     $u_name = $user->u_name;
            //     $propertyid = $user->propertyid;
            //     $company = DB::table('company')->where('email', $mail)->where('propertyid', $propertyid)->where('u_name', $u_name)->first();
            //     if ($company) {
            //         $view->with('user', $company);
            //     }
            // }

            $companies = Company::where('propertyid', Auth::user()->propertyid)->orderBy('comp_code', 'ASC')->get();
            $view->with('companies', $companies);
        });
    }
}
