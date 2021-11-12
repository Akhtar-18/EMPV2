<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Notifications\NewUser;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserNotify;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register'], 'verified']);
    }


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(),
        [
           //'name' => 'required|string|between:2,255|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|string|email|max:255|unique:users|regex:/(.*)@ghrix\.com/i',
            //'password' => 'required|string|confirmed|min:8|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password' => ['required', 'string', 'max:25', Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()],
        ],
        [
            //'name.required' => 'Name cannot be blank',
            //'name.regex' => 'Name contain alphabets only',
            //'name.between' => 'Name will start from 2 chars and not exceed 255 chars',
            'email.required' => 'Email cannot be blank',
            'email.unique' => 'Email already exists',
            'email.regex' => 'Email is not with @ghrix.com',
            'password.required' => 'Password cannot be blank',
            'password.min' => 'Password must be atleast 8 chars',
            'password.max' => 'Password should not exceed 25 chars',
            //'password.regex' => 'Password should contain 1 uppercase letter, 1 lowercase letter, 1 numeric letter and  1 symbol',
            //'password.confirmed' => 'Password confirmation does not match'
        ]
    );

        if($validator->fails()){
            //return response()->json($validator->errors(), 400);
            //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
            return response()->json(["success" => false, "message" => $validator->errors()],400);
        }

        $user = User::create(array_merge(
                    $validator->validated()
                ));

        /*$user = User::create(array_merge(                 //Not Supporting
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        )); */

        //$user->sendEmailVerificationNotification();   //For sending email verification
        //$email = $request->get('email');

        //Mail::to($email)->send(new WelcomeMail($user));
        $user->notify(new UserNotify($user));

        $admin = User::where('role','admin')->first();
        if ($admin) {
        $admin->notify(new NewUser($user));
        }

        return response()->json([
            "success" => true,
            "message" => 'Account request received, we will notify you when the account is approved',
            "user" => $user
        ], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
            //'password' => 'required|string|min:8|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password' => ['required', 'string', 'max:25', Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()],
        ],
        [
            'email.required' => 'Email cannot be blank',
            'email.regex' => 'Email is not with @ghrix.com',
            'password.required' => 'Password cannot be blank',
            'password.min' => 'Password must be atleast 8 chars',
            'password.max' => 'Password should not exceed 25 chars',
            //'password.regex' => 'Password should contain 1 uppercase letter, 1 lowercase letter, 1 numeric letter and  1 symbol'
        ]
    );

        //$approve = User::where("email", $request->email)->where("password", md5($request->password))->first();
        //$user = auth()->user();
        //$users = User::where('email', '=', $request->input('email'))->first();
        /*

        $user = User::where('email', '=', Input::get('email'))->first();
if ($user === null) {
   // user doesn't exist
}
And if you only want to check

if (User::where('email', '=', Input::get('email'))->count() > 0) {
   // user found
}
Or even nicer

if (User::where('email', '=', Input::get('email'))->exists()) {
   // user found
}

        */


        $users = User::where('email', '=', $request->input('email'))->where("approved", 1)->first();
        $users1 = User::where('email', $request->input('email'))->first();
        //$users2 = User::where('email', $request->input('email'))->where("email_verified_at")->first();  //For email verification
        /*if ($users2) {
            return response()->json([
                'success'=>false,
            'message' => 'Email not verified!'], 400);

        } */

        if ($users1 === null) {
            // User does not exist
            if($validator->fails()){
                //return response()->json($validator->errors(), 400);
                //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
                return response()->json(["success" => false, "message" => $validator->errors()], 400);
            }
            return response()->json([
                'success'=>false,
            'message' => 'Email not found in our database!'], 400);
        }

        if ($users === null) {
            // User account not activated
            return response()->json([
                'success' => false,
                'message' => 'Your account has not activated!'], 400);
        }
            // User exists
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Errors',
                    'errors' => $validator->errors()
                ], 400);
            }

            if (! $token = auth()->attempt($validator->validated())) {
                return response()->json([
                    'success' => false,
                    'message' => 'User authentication failed!'
                ], 400);
            }
            return $this->createNewToken($token);



    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'User logout successfully!'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
       // return response()->json(auth()->user());
        return response()->json([
            'success' => true,
            'message' => 'User Details',
            'user' => auth()->user()
        ],200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'success' => true,
            'message' => 'User login succesfully, Use token to authenticate!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ],200);
    }

    public function approve($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->update(['approved' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'User Approved!',
            'user' => auth()->user()
        ],200);
    }
}
