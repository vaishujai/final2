<?php
namespace App\Http\Controllers\Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Mail\EmailVerificationMailable;
use Illuminate\Support\Facades\Mail;
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
    protected $redirectTo = '/home';
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verifyToken' => str_random(40)
        ]);
        Mail::to($user->email)->send(new EmailVerificationMailable($user));
        return $user;
    }
    public function verifyEmailFirst($verifyToken)
    {
        $verifyEmailFirst = User::where('verifyToken', $verifyToken)->first();
        if(isset($verifyEmailFirst) ){
            $user = $verifyEmailFirst;
            if(!$user->email_verified_at) {
                $verifyEmailFirst->email_verified_at = date('Y-m-d H:i');
                $verifyEmailFirst->save();
                $status = "Email is verified successfully. You can continue to Login.";
            }else{
                $status = "Email has been successfully verified earlier. You can continue to Login";
            }
        }else{
            return redirect('/login')->with('warning', "There is an error with identification of your email id.");
        }
        return redirect('/login')->with('status', $status);
    }
    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status', 'Verification link has been sent to your mail. Please click on it to login.');
    }
}