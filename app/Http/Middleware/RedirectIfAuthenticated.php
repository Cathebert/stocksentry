<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, string ...$guards): Response
    {
        //dd($request);
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                switch(Auth::user()->authority){
         case 1:
        
            return redirect(route('admin.home'));
                break;
                 case 2:
                 
            return redirect(route('moderator.home'));
                break;

            case 3:
                
             return redirect(route('user.home'));
                break;
         
              case 4:
                return redirect(route('cold.home'));
                break;
                default:
             
                // $this->redirectTo ='/';
            return redirect('/');
                break;

        }
                
            }
            else{
                 Auth::logout();
 
    $request->session()->invalidate();
 
    $request->session()->regenerateToken();
 
   
            }
          
        }

        return $next($request);
    }
}
