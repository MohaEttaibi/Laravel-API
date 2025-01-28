<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use App\Mail\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use App\Models\Brand;

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

    public function admin_forgot_password(Request $request){
        if($request->isMethod('post')){
            $user = User::where('email', '=', $request->email)->first();
            if(isset($user) && $user->hasRole('admin')) {
                $data = Mail::to($user->email)->send(new ForgetPassword($user->id));
                return redirect()->back()->with('msg', 'Check you mail to reset password.');
                // dd($data);
            } else {
                return redirect()->back()->with('msg', 'Email Not Found.');
            }
            return $request->all();
        } else {
            return redirect()->route('login');
        };
    }    

    public function admin_reset_password($id) {
        return view('auth.reset-password', compact('id'));
    }

    public function admin_update_password(Request $request) {
        $validate = $request->validate([
            'password' => 'required',
        ]);
        $id         = $request->id;
        $password   = Hash::make($request->password);

        $user = User::where('id', '=', $id)->update([
            'password' => $password
        ]);
        return redirect()->back()->with('msg', 'Password is updated.');
    }

    public function dashboard(){
        $brand = Brand::count('id');
        return view('dashboard', compact('brand'));
    }
}
