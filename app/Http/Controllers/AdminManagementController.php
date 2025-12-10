<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::with('roles')->get();
        return view('admin.master-data.admin-management.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.master-data.admin-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id_role'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Attach roles
            $user->roles()->attach($request->roles);

            return redirect()->route('admin-management.index')
                ->with('success', 'Admin berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan admin: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $admin = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.master-data.admin-management.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id_role'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $admin->update($updateData);

            // Sync roles
            $admin->roles()->sync($request->roles);

            return redirect()->route('admin-management.index')
                ->with('success', 'Admin berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui admin: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $admin = User::findOrFail($id);
            
            // Prevent deleting current user
            if ($admin->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus akun yang sedang aktif!');
            }

            $admin->delete();

            return redirect()->route('admin-management.index')
                ->with('success', 'Admin berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus admin: ' . $e->getMessage());
        }
    }
}

