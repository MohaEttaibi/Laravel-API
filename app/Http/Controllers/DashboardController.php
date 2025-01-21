<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin_login(Request $request){
        if($request->isMethod('post')) {
            $validated = $request->validate([
                'email'     => 'required',
                'password'  => 'required',
            ], [
                'email.required' => 'Please provide an email.',
                'password.required' => 'Please provide an password.',
            ]);
            $email = $request->email;
            $password = $request->password;
            $user = User::where('email', '=', $email)->first();
            if(isset($user) && $user->hasRole('admin')) {
                if(Auth::attempt(['email' => $email, 'password' => $password])) {
                    $request->session()->regenerate();
                    return redirect()->intended('dashboard');
                } else {
                    return redirect()->back()->with('msg', 'Password Incorrect.');    
                }
            } else {
                return redirect()->back()->with('msg', 'You don\'t have access.');
            }
            return $request->all();

        } else {
            return redirect()->route('login');
        };
    }
}
