<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Permission::latest()->get();
        return response()->json([PermissionResource::collection($data),'Permissions fetched!']);
    }

    public function createPermission(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions,name',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $permission = Permission::create([
            'name' => $request->name,
         ]);

        return response()->json(['Permission created successfully.', new PermissionResource($permission)]);
    }

    public function updatePermission(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions,name',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->save();

        return response()->json(['Permission updated successfully.', new PermissionResource($permission)]);
    }

    public function showPermission($id)
    {
        $permission = Permission::find($id);
        if(is_null($permission)) {
            return response()->json('Data not found',400);
        }
        return response()->json([new PermissionResource($permission)]);
    }

    public function deletePermission($id)
    {
        Permission::find($id)->delete();
        return response()->json('Permission deleted successfully');
    }
}
