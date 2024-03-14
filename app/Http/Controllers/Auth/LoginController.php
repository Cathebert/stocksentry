<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\Services\LogActivityService;
use App\Models\Laboratory;
use Illuminate\Http\Request;
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
    protected $redirectTo ;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function redirectTo(){
       LogActivityService::saveToLog('Logged in','Member with name  '.auth()->user()->name.' '.auth()->user()->last_name.' logged in of system.','low');
        switch(Auth::user()->authority){
          
 case 1:
                 $this->redirectTo = route('admin.home');
            return $this->redirectTo;
                break;
    case 2:
                 $this->redirectTo = route('moderator.home');
            return $this->redirectTo;
                break;
    case 3:
                 $this->redirectTo =route('user.home');
            return $this->redirectTo;
                break;
case 4:

 $this->redirectTo =route('cold.home');
            return $this->redirectTo;
                break;
        default:
             
                 $this->redirectTo =route('user.login');
            return $this->redirectTo;
                break;

        }
    }
    public function showProviderForm(){
         $data['laboratories']=Laboratory::select('lab_name','lab_code')->get();
        return view('auth.provider.login',$data);
    }

      public function LoginForm(){
        $data['laboratories']=Laboratory::select('lab_name','lab_code')->get();

        return view('auth.student.login',$data);
    }

      protected function adminLogin(Request $request)
    {
          $errors = [$this->username() => trans('auth.failed')];
       $this->validate($request,[
            'username'=>'required',
            'password'=>'required| min:8'
        ]);
        if(Auth::attempt(['username'=>$request->username,'password'=>$request->password])){
          LogActivityService::saveToLog('Logged in','Member with username  '.auth()->user()->username.' logged in of system.','low');
           $this->redirectTo();
        }
         return $this->sendFailedLoginResponse($request);
        

        //return back()->withInput($request->only('username'));
    }
 protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()->withInput($request->only('username', 'remember'))->withErrors([
            'username' => 'These credentials do not match our records.',
        ]);
    }
    public function logout(Request $request){
         LogActivityService::saveToLog('Logged out','Member with username  '.auth()->user()->name.' '.auth()->user()->last_name.' logged out of system.','low');
         Auth::logout();
 
    $request->session()->invalidate();
 
    $request->session()->regenerateToken();
    }

}
