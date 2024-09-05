<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\DepartmenUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class UnitController extends Controller
{
    function index(){
        return view('entity.departments.index',[
            'departments' => Unit::all(),
        ]);
    }

    function create(){
        return view('entity.departments.create');
    }

    function store(Request $request){
        try{
            $request->validate([
                'name' => ['required','string','max:255','unique:units']
            ]);

            Unit::create($request->all());
            return redirect('/department')->with('success', 'The department has been successfully added.');
        }catch(ValidationException $e){
            if ($e->validator->errors()->has('name')) {
                return back()->withErrors(['name' => 'This department is already registered.']);
            }
        }
    }

    function edit(Unit $unit){
        return view('entity.departments.edit', [
            'department' => $unit
        ]);
    }

    function update(Request $request, Unit $unit){
        try{
            $request->validate([
                'name' => ['required','string','max:255','unique:units,name,'.$unit->id]
            ]);

            $unit->update($request->all());
            return redirect('/department')->with('success', 'The department has been successfully updated.');
        }catch(ValidationException $e){
            if ($e->validator->errors()->has('name')) {
                return back()->withErrors(['name' => 'This department is already registered.']);
            }
        }
    }

    function destroy(Unit $unit) {
       // Cek apakah unit_id digunakan di tabel DepartmenUser
    $department_user_exists = DepartmenUser::where('unit_id', $unit->id)->exists();
    if ($department_user_exists) {
        return back()->withErrors(['unit_id' => 'This department is currently assigned to a department employee.'])->withInput();
    }

    // Cek apakah unit_id digunakan di tabel User
    $user_exists = User::where('unit_id',  $unit->id)->exists();
    if ($user_exists) {
        return back()->withErrors(['unit_id' => 'This department has employees assigned.'])->withInput();
    }

    // Hapus unit jika tidak terhubung dengan DepartmenUser atau User
    $unit->delete();
    return redirect('/department')->with('success',  'The department has been successfully deleted.');
    }
}
