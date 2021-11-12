<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;

class ApiPermissionController extends Controller
{
    /**
     * create a new instance of the class
     *
     * @return void
     */
    public function __construct()
    {
         $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index','store']]);
         $this->middleware('permission:permission-create', ['only' => ['create','store']]);
         $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = Permission::latest()->get();
        //return response()->json([PermissionResource::collection($data),'Permissions fetched!']);
        return response()->json([
            "success" => true,
            "message" => 'Permissions fetched successfully.',
            "permission" => $permission
        ], 200);
    }


    /**
     * Store a newly created permission in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions,name|string|between:2,255|regex:/^[a-z-_]+$/',
        ],
        [
            'name.required' => 'Permission name cannot be blank',
            'name.unique' => 'Permission name must be unique',
            'name.between'=>'Permission name will start from 2 chars and not exceed 255 chars',
            'name.regex'=>'Permission name will not include blank spaces, digits or uppercase chars'
        ]

        );

        if($validator->fails()){
            //return response()->json($validator->errors());
            return response()->json(["success" => false, "message" => $validator->errors()],400);
        }

        $permission = Permission::create([
            'name' => $request->name,
         ]);

        //return response()->json(['Permission created successfully.', new PermissionResource($permission)]);
        return response()->json([
            "success" => true,
            "message" => 'Permission created successfully.',
            "permission" => $permission
        ], 200);
    }

    /**
     * Display the specified permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::find($id);
        if(is_null($permission)) {
            //return response()->json('Data not found',400);
            return response()->json(["success" => false, "message" => 'Data not found'],400);
        }
        //return response()->json([new PermissionResource($permission)]);
        return response()->json([
            "success" => true,
            "message" => "Permission retrieved successfully.",
            "permission" => $permission
            ],200);
    }


    /**
     * Update the specified permission in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,255|regex:/^[a-z-_]+$/',
        ],
        [
            'name.required' => 'Permission name cannot be blank',
            'name.unique' => 'Permission name must be unique',
            'name.between'=>'Permission name will start from 2 chars and not exceed 255 chars',
            'name.regex'=>'Permission name will not include blank spaces, digits or uppercase chars'
        ]
        );

        if($validator->fails()){
            //return response()->json($validator->errors());
            return response()->json(["success" => false, "message" => $validator->errors()],400);
        }

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->save();

        //return response()->json(['Permission updated successfully.', new PermissionResource($permission)]);
        return response()->json([
            "success" => true,
            "message" => "Permission updated successfully.",
            "permission" => $permission
            ],200);
    }

    /**
     * Remove the specified permission from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Permission::find($id)->delete();
        //return response()->json('Permission deleted successfully');
        return response()->json([
            "success" => true,
            "message" => 'Permission deleted successfully.'
        ], 200);
    }
}
