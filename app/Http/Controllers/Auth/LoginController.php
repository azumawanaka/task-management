<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '/tasks';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function __invoke()
    {
        return view('layouts.auth.app');
    }

    public function login(Request $request)
    {
        $usernameOrEmail = $request->input('username_or_email');

        // Check if the input is an email address
        $isEmail = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'username_or_email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Define the login credentials
        $credentials = [
            $isEmail ? 'email' : 'user_name' => $usernameOrEmail,
            'password' => $request->password,
        ];

        // Attempt to authenticate the user
        if (auth()->attempt($credentials, $request->filled('remember'))) {
            // Authentication successful, generate token
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
           // Check if the account exists
           $user = User::where('email', $usernameOrEmail)
                ->orWhere('user_name', $usernameOrEmail)
                ->first();

           if ($user) {
               // Account exists but authentication failed
               return response()->json(['error' => 'Invalid credentials'], 401);
           } else {
               // Account does not exist
               return response()->json(['error' => 'Account does not exist'], 404);
           }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login')->withHeaders([
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
        ]);
    }
}
