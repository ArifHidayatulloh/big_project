<?php

namespace App\Http\Controllers;

use App\Models\DepartmenUser;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;

class DepartmentUserController extends Controller
{
    function index()
    {
        return view('entity.departments-users.index', [
            'depUsers' => DepartmenUser::paginate(10),
        ]);
    }

    function create()
    {
        return view('entity.departments-users.create', [
            'users' => User::all(),
            'departments' => Unit::all(),
        ]);
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required'],
            'unit_id' => ['required'],
        ]);

        $cekDepUsers = DepartmenUser::firstOrNew($data);

        if ($cekDepUsers->exists) {
            return back()->withErrors(['data' => 'A user with this department already exists.'])->withInput();
        } else {
            DepartmenUser::create($data);
            return redirect('/depuser')->with('success', 'Department user has been successfully added.');
        }
    }

    function edit(DepartmenUser $depUser)
    {
        return view('entity.departments-users.edit', [
            'depUser' => $depUser,
            'users' => User::all(),
            'departments' => Unit::all(),
        ]);
    }

    function update(Request $request, DepartmenUser $depUser)
    {
        // Validasi input
        $data = $request->validate([
            'user_id' => ['required'],
            'unit_id' => ['required'],
        ]);

        // Cek apakah kombinasi user_id dan unit_id sudah ada, kecuali untuk data yang sedang di-update
        $cekDepUsers = DepartmenUser::where('user_id', $data['user_id'])
            ->where('unit_id', $data['unit_id'])
            ->where('id', '!=', $depUser->id)
            ->exists();

        if ($cekDepUsers) {
            return back()->withErrors(['data' => 'A user with this department already exists.'])->withInput();
        } else {
            // Update data department user
            $depUser->update($data);

            return redirect('/depuser')->with('success', 'Department user has been successfully updated.');
        }
    }

    function destroy(DepartmenUser $depUser){
        $depUser->delete();
        return redirect('/depuser')->with('success', 'Department user has been successfully deleted.');
    }
}
