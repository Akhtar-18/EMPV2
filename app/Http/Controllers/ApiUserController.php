<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::latest()->get();
        //return response()->json([UserResource::collection($data),'Users fetched!']);
        return response()->json([
            "success" => true,
            "message" => 'Users fetched successfully.',
            "user" => $user
        ], 200);
    }

    /**
     * Store a newly created user in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input); */

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
            'roles' => 'required|integer|between:1,50'
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
            'roles.required' => 'Role cannot be blank',
            'roles.integer' => 'Role must be of integer type',
            'roles.between' => 'Role will range in between 1-50 digits'
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

        $user->assignRole($request->input('roles'));

        //return response()->json(['User created successfully.', new UserResource($user)]);

        return response()->json([
            "success" => true,
            "message" => 'User created',
            "user" => $user
        ], 200);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if(is_null($user)) {
            //return response()->json('Data not found',400);
            return response()->json(["success" => false, "message" => 'Data not found'],400);
        }
        //return response()->json([new UserResource($user)]);
        return response()->json([
            "success" => true,
            "message" => "User retrieved successfully.",
            "user" => $user
            ],200);
    }


    /**
     * Update the specified user in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*$this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'confirmed',
            'roles' => 'required'
        ]);

        $input = $request->all();

        if(!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        } */
        $validator = Validator::make($request->all(),
        [
           //'name' => 'required|string|between:2,255|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|string|email|max:255|regex:/(.*)@ghrix\.com/i',
            //'password' => 'required|string|confirmed|min:8|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'password' => ['required', 'string', 'max:25', Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()],
            'roles' => 'required|integer|between:1,50'
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
            'roles.required' => 'Role cannot be blank',
            'roles.integer' => 'Role must be of integer type',
            'roles.between' => 'Role will range in between 1-50 digits'
        ]
        );
        $input = $request->all();

        $user = User::find($id);
        $user->update($input);

        /*DB::table('model_has_roles')
            ->where('model_id', $id)
            ->delete(); */
            if(is_null($user)) {
                //return response()->json('Data not found',400);
                return response()->json(["success" => false, "message" => 'Data not found'],400);
            }
            else
            {
                $user->assignRole($request->input('roles'));
            }

        //$user->assignRole($request->input('roles'));

        //return response()->json(['User updated successfully.', new UserResource($user)]);
        return response()->json([
            "success" => true,
            "message" => "User updated successfully.",
            "user" => $user
            ],200);
    }

    /**
     * Remove the specified user from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return response()->json('User deleted successfully');
    }
}
