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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Role::latest()->get();
        return response()->json([RoleResource::collection($data),'Roles fetched!']);
    }

    public function createRole(Request $request)
    {
        /*$this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.'); */

        /*$validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles',
            'permission' => 'required',
        ]); */

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

            $role = Role::create([
                'name' => $request->name
             ]);
             $role->syncPermissions($request->input('permission'));

            return response()->json(['Role created successfully.', new RoleResource($role)],200);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRole($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_has_permissions.role_id',$id)
            ->get();

        if(is_null($rolePermissions)) {
            return response()->json('Data not found',400);
        }
        return response()->json([new RoleResource($rolePermissions)]);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return response()->json(['Role updated successfully.', new RoleResource($role)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteRole($id)
    {
        Role::find($id)->delete();
        return response()->json('Role deleted successfully');
    }


}
