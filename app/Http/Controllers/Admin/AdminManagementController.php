<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = Admin::orderBy('id', 'desc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_super_admin' => $request->has('is_super_admin') ? 1 : 0,
            'is_2fa_exempt' => $request->has('is_2fa_exempt') ? 1 : 0,
            'status' => $request->has('status') ? 1 : 0,
            'data' => 1,
        ]);

        return back()->with('success', 'Admin account created successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $admin = Admin::findOrFail($request->id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,'.$admin->id,
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_super_admin' => $request->has('is_super_admin') ? 1 : 0,
            'is_2fa_exempt' => $request->has('is_2fa_exempt') ? 1 : 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        // Prevent Super Admin from downgrading themselves to standard admin
        if (auth('admin')->id() == $admin->id && !isset($request->is_super_admin)) {
            $updateData['is_super_admin'] = 1;
            $warning = " You cannot remove your own Super Admin privileges.";
        }

        $admin->update($updateData);

        return back()->with('success', 'Admin account updated successfully.' . ($warning ?? ''));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        if (auth('admin')->id() == $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return back()->with('success', 'Admin account permanently deleted.');
    }
}
