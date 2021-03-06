<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.3/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('UserActivation');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        //return view('adminlte::home');
        $user = Auth::user();
        $role = $user->getRoleNames();

        if(isset($role[0]) && ($role[0]=='admin') ){
           return redirect('admin');
        }
        elseif(isset($role[0]) && ($role[0]=='superadmin')){

            return redirect('superadmin');

        }elseif(isset($role[0]) && ($role[0]=='user')){

            return redirect('users');
        }

    }

    public function AdminView(){

        $user = Auth::user();
        $role = $user->getRoleNames();

        return view('adminlte::adminview',compact('role'));

    }

    public function SuperAdminView(){

        $user = Auth::user();
        $role = $user->getRoleNames();

        return view('adminlte::superadminview',compact('role'));

    }


    public function UserView(){

        $user = Auth::user();
        $role = $user->getRoleNames();

        $users = User::all();
        return view('adminlte::home',compact('role','users'));

    }

}