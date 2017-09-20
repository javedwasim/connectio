<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Roles;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Hash;
use Jenssegers\Agent\Agent;
use App\usermeta;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\UserRoles;
use App\UserPermissions;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->getRoleNames();

        $users = User::all();
        return view('adminlte::home', compact('role', 'users'));
    }


    // CRUDController.php
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Roles::all();
        $permissions = Permission::all();

        $result = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('model_has_permissions', 'model_has_permissions.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
            ->select('users.*','roles.id as rid', 'permissions.id as pid', 'roles.name as rname','permissions.name as pname')
            ->where('users.id', '=', $id)
            ->get();

        $user = $result[0];
        return view('adminlte::user', compact('user', 'id','roles','permissions'));

    }

    public function update($id)
    {

        $request = Request::all();

        $user = User::find($request['userid']);
        $role = $user->getRoleNames();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if (isset($request['password'])) {
            $user->password = Hash::make($request['password']);
        }
        $user->activated = $request['activated'];
        $user->updated_at = Carbon::now();;
        $user->save();

        $role = $request['role_id'];
        UserRoles::where('model_id', $id)->update(['role_id' => $role]);
        $permission = $request['permission_id'];
        UserPermissions::where('model_id', $id)->update(['permission_id' => $permission]);

        return redirect('users');

    }

    public function create()
    {
        $roles = Roles::all();
        $permissions = Permission::all();
        return view('adminlte::usercreate', compact('roles', 'permissions'));

    }


    public function store(Request $request)
    {
        $request = Request::all();

        $user = new User();
        $role = $user->getRoleNames();
        $user->name = $request['name'];
        $user->email = $request['email'];
        if (isset($request['password'])) {
            $user->password = Hash::make($request['password']);
        }
        $user->activated = $request['activated'];
        $user->save();

        $role = $request['role'];
        $user->assignRole($role);
        //adding permissions to a user
        $permission = $request['permission'];
        $user->givePermissionTo($permission);
        //user id $user->id
        //save userlocation info
        $this->SaveUserMeta($user->id, "182.185.148.11");
        //send verification email.

        return redirect('users');

    }

    public function SaveUserMeta($userId, $ipAddres)
    {

        $userLoacationInfo = geoip()->getLocation($ipAddres);

        $agent = new Agent();
        $deviceType = '';

        $browser = $agent->browser(); //Get the browser name. (Chrome, IE, Safari, Firefox, ...)
        $version = $agent->version($browser);

        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);

        if ($agent->isDesktop()) {

            $deviceType = 'Desktop';

        } else {

            $deviceType = $agent->device(); //Get the device name, if mobile. (iPhone, Nexus, AsusTablet, ...)
        }

        $platform = $agent->platform(); //Get the operating system. (Ubuntu, Windows, OS X, ...)

        $browserInfo = json_encode(array('browser' => $browser, 'version' => $version,
            'platform' => $platform, 'platformversion' => $platformVersion, 'platform' => $platform,
            'devicetype' => $deviceType));


        $userInfo = json_encode(array('ip' => $userLoacationInfo->ip, 'iso_code' => $userLoacationInfo->iso_code,
            'country' => $userLoacationInfo->country, 'city' => $userLoacationInfo->city, 'state' => $userLoacationInfo->state,
            'state_name' => $userLoacationInfo->state_name, 'postal_code' => $userLoacationInfo->postal_code,
            'lat' => $userLoacationInfo->lat, 'lon' => $userLoacationInfo->lon, 'timezone' => $userLoacationInfo->timezone,
            'currency' => $userLoacationInfo->currency));

        $userMeta = new usermeta();
        $userMeta->user_id = $userId;
        $userMeta->locations = $userInfo;
        $userMeta->browserinfo = $browserInfo;
        $userMeta->save();
        return $userMeta;


    }

    public function destroy($id)
    {
        dd($id);
        $user = User::find($id);
        $user->delete();

    }


}
