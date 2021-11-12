<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;

class ApiRoleController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::latest()->get();
        //return response()->json([RoleResource::collection($data),'Roles fetched!']);
        return response()->json([
            "success" => true,
            "message" => 'Roles fetched successfully.',
            "role" => $role
        ], 200);
    }


    /**
     * Store a newly created role in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles,name|string|between:2,255|regex:/^[a-z ]+$/',
            'permission' => 'required|array',
            'permission.*' => 'distinct|integer|between:1,50'
        ],
        [
            'name.required' => 'Role name cannot be blank',
            'name.unique' => 'Role name must be unique',
            'name.regex' => 'Role name contain lowercase chars only and not contain digits or uppercase chars',
            'name.between' => 'Role name will start from 2 chars and not exceed 255 chars',
            'permission.required' => 'Permission cannot be blank',
            'permission.array' => 'Permission will be of integer array type',
            'permission.*.distinct' => 'Permission must include distict values',
            'permission.*.integer'=>'Permission must include integer values only',
            'permission.*.between' => 'Permission array will contain digits in between 1 - 50'
        ]
        );

        if($validator->fails()){
            //return response()->json($validator->errors(), 400);
            //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
            return response()->json(["success" => false, "message" => $validator->errors()],400);
        }

            $role = Role::create([
                'name' => $request->name
             ]);
             $role->syncPermissions($request->input('permission'));

            //return response()->json(['Role created successfully.', new RoleResource($role)],200);

            return response()->json([
                "success" => true,
                "message" => 'Role created successfully.',
                "role" => $role
            ], 200);
    }

    /**
     * Display the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$id)
            ->get();

        //$role->syncPermissions($rolePermissions);

        if(is_null($role)) {
            //return response()->json('Data not found',400);
            return response()->json(["success" => false, "message" => 'Data not found'],400);
        }
        else
        {
            $role->syncPermissions($rolePermissions);
        }
        //return response()->json([new RoleResource($role, $rolePermissions)]);
        return response()->json([
            "success" => true,
            "message" => "Role retrieved successfully.",
            "role" => $role
            ],200);

    }


    /**
     * Update the specified role in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,255|regex:/^[a-z ]+$/',
            'permission' => 'required|array',
            'permission.*' => 'distinct|integer|between:1,50'
        ],
        [
            'name.required' => 'Role name cannot be blank',
            'name.regex' => 'Role name contain lowercase chars only and not contain digits or uppercase chars',
            'name.between' => 'Role name will start from 2 chars and not exceed 255 chars',
            'permission.required' => 'Permission array cannot be blank',
            'permission.array' => 'Permission will be of integer array type',
            'permission.*.distinct' => 'Permission array must include distict values',
            'permission.*.integer'=>'Permission array must include integer values only',
            'permission.*.between' => 'Permission array will start from 1 digit and not exceed 50 digits'
        ]
        );

        if($validator->fails()){
            //return response()->json($validator->errors(), 400);
            //return response()->json(["success" => false, "message" => "Validation Errors","errors" => $validator->errors()],400);
            return response()->json(["success" => false, "message" => $validator->errors()],400);
        }

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        //return response()->json(['Role updated successfully.', new RoleResource($role)]);

        return response()->json([
            "success" => true,
            "message" => 'Role updated successfully.',
            "role" => $role
        ], 200);
    }

    /**
     * Remove the specified role from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::find($id)->delete();
        //return response()->json('Role deleted successfully');
        return response()->json([
            "success" => true,
            "message" => 'Role deleted successfully.'
        ], 200);
    }
}
