<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\User;
use Validator;
use Mail;
use Hash;
use Flash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginController extends Controller
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.registers');
    }



    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        $data = $request->all();
        $email = $data['email'];
        //Auth::guard($this->getGuard())->login($this->create($request->all()));
        //return redirect($this->redirectPath());
        $this->create($request->all());
        return redirect($this->redirectPath($email));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'terms' => 'required',
        ]);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return string|null
     */
    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : null;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $confirmation_code = str_random(30);

        //create user
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->confirmation_code = $confirmation_code;

        $user->save();

        //adding roles to a user
        $user->assignRole('admin');
        //adding permissions to a user
        $user->givePermissionTo('administrator');
        //user id
        $insertedId = $user->id;

        //send verification email.
        $data = [

            'confirmation_code' => $confirmation_code
        ];

        Mail::send('auth.emails.verify', $data, function ($message) {

            $message->from('info@osterleycc.com', 'Osterley Cricket Club');

            $message->to(Input::get('email'))->subject('Verify your email address');

        });


        return $user;

    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath($email)
    {
        if (property_exists($this, 'redirectPath')) {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/registered/'.$email;
    }


    public function confirm($confirmationCode)
    {
        if( ! $confirmationCode)
        {
            Flash::message('You have successfully verified your account.');

        }

        $user = User::where('confirmation_code',$confirmationCode)->first();

        if ( ! $user)
        {
            return redirect('registers')->with('status', 'Invalid email address! Please signup.');

        }

        $user->activated = 1;
        $user->confirmation_code = null;
        $user->save();

        return redirect('login')->with('status', 'Account activated successfully! Please Login.');
    }

    public function showRegistrationLandingPage($email){

        return view('auth.emails.landing',compact('email'));

    }
}
