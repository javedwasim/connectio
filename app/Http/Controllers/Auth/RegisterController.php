<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Mail;
use Hash;
use Flash;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Request;
use App\usermeta;
use Jenssegers\Agent\Agent;

/**
 * Class RegisterController
 * @package %%NAMESPACE%%\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('adminlte::auth.register');
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'username' => 'sometimes|required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'terms' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $userData = Request::all();
        $confirmationCode = str_random(30);

        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ];
        if (config('auth.providers.users.field', 'email') === 'username' && isset($data['username'])) {
            $fields['username'] = $data['username'];
        }

        //return User::create($fields);
        //create user with role and permisssion
        $user = new User;
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->confirmation_code = $confirmationCode;
        $user->save();

        //adding roles to a user
        $user->assignRole('user');
        //adding permissions to a user
        $user->givePermissionTo('edit not allowed');
        //user id $user->id
        //save userlocation info
        $this->SaveUserMeta($user->id, "182.185.148.11");
        //send verification email.
        $this->SendActivationEmail($confirmationCode);

        return $user;

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

    public function SendActivationEmail($confirmationCode)
    {

        $data = [

            'confirmation_code' => $confirmationCode
        ];

        Mail::send('auth.emails.verify', $data, function ($message) {

            $message->from('info@connectio.com', 'Connect IO');

            $message->to(Input::get('email'))->subject('Account Activation');

        });


    }

    public function confirm($confirmation_code)
    {
        if (!$confirmation_code) {
            Flash::message('You have successfully verified your account.');

        }

        $user = User::whereConfirmationCode($confirmation_code)->first();

        if (!$user) {
            return redirect('registers')->with('status', 'Invalid email address! Please signup.');

        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

        return redirect('login')->with('status', 'Account activated successfully! Please Login.');
    }
}
