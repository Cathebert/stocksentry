<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

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
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo ;
public function redirectTo(){
        switch(Auth::user()->role){
            case 1:
                 $this->redirectTo = '/admin';
            return $this->redirectTo;
                break;
           case 2:
                 $this->redirectTo = route('moderator.home');
            return $this->redirectTo;
                break;

                case 3:
                 $this->redirectTo = route('admin.home');
            return $this->redirectTo;
                break;
                default:
             
                 $this->redirectTo ='/';
            return $this->redirectTo;
                break;

        }
    }
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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
         
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
       //dd($data);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'authority'=>$data['role'],
            'password' => Hash::make($data['password']),
        ]);
    }
    public function showProviderRegistrationForm()
    {
        return view('auth.provider.register');
    }
     public function showStudentRegistrationForm()
    {
        return view('auth.student.register');
    }
}
