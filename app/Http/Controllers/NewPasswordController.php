<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password As RulesPassword;
use Illuminate\Support\Str;
//use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class NewPasswordController extends Controller
{

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
           //'email' => 'required|email',
           'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
        ],
        [
            'email.required' => 'Email cannot be blank',
            'email.regex' => 'Email is not with @ghrix.com',
        ]
        );

        /*$request->validate([
            //'email' => 'required|email',
            'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
        ],
        [
            'email.required' => 'Email cannot be blank',
            'email.regex' => 'Email is not with @ghrix.com',
        ]
    ); */


        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
           /* return [
                'status' => __($status)
            ]; */
            return response()->json([
                'success'=>true,
                'message' => 'We have emailed your password reset link!'
            ], 200);
        }
        else{
            if($validator->fails()){
                //return response()->json($validator->errors(), 400);
                //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
                return response()->json(["success" => false, "message" => $validator->errors()],400);
            }
            return response()->json([
                'success'=>false,
                'message' => 'Email not found in our database!'
            ], 400);
        }

        /*throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]); */
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
            //'password' => ['required', 'confirmed', RulesPassword::defaults()],
            'password' => 'required|string|min:8|max:25|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
        ]);

        $validator = Validator::make($request->all(),
        [
            'token' => 'required',
            'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
            //'password' => ['required', 'confirmed', RulesPassword::defaults()],
            'password' => ['required|string|min:8|max:25|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',RulesPassword::defaults()],
        ],
        [
            'email.required' => 'Email cannot be blank',
            'email.regex' => 'Email is not with @ghrix.com',
            'password.required' => 'Password cannot be blank',
            'password.min' => 'Password must be atleast 8 chars',
            'password.max' => 'Password should not exceed 25 chars',
            'password.regex' => 'Password should contain 1 uppercase letter, 1 lowercase letter, 1 numeric letter and  1 symbol',
        ]
        );

        $status = Password::reset(
            $request->only('email', 'password','token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'success' => true,
                'message' => 'Password has been reset!'
            ], 200);
        } else {
            if($validator->fails()){
                //return response()->json($validator->errors(), 400);
                //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
                return response()->json(["success" => false, "message" => $validator->errors()],400);
            }
            return response()->json([
                'success'=>false,
                'message' => 'Either your email or token is wrong!'
            ], 400);
        }

       /* return response([
            'message'=> __($status)
        ], 500); */

    }


}
