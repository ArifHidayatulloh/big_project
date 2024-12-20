<?php

namespace App\Http\Controllers;

use App\Models\DepartmenUser;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    function index(Request $request)
    {
        $search = null;
        $search = $request->get('search');
        $users = User::query()->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('nik', 'like', '%' . $search . '%');
        })->paginate(25);
        return view('entity.users.index', compact('users', 'search'));
    }

    function create()
    {
        return view('entity.users.create', [
            'departments' => Unit::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'nik' => ['required', 'unique:users'],
                'name' => ['required'],
                'gender' => ['required'],
                'phone' => ['nullable'],
                'email' => ['nullable', 'unique:users'],
                'password' => ['required'],
                'role' => ['required'],
                'join_date' => ['nullable'],
                'address' => ['nullable'],
                'unit_id' => ['nullable'],
                'access_worklist' => ['nullable'],
                'access_control_budget' => ['nullable'],
                'access_payment_schedule' => ['nullable'],
            ]);


            $data['password'] = bcrypt($data['password']);

            $data['access_worklist'] = $request->has('access_worklist') ? 1 : 0;
            $data['access_control_budget'] = $request->has('access_control_budget') ? 1 : 0;
            $data['access_payment_schedule'] = $request->has('access_payment_schedule') ? 1 : 0;

            User::create($data);

            return redirect('/user')->with('success', 'Employee has been successfully added.');
        } catch (ValidationException $e) {
            if ($e->validator->errors()->has('nik')) {
                return back()->withErrors(['nik' => 'This NIK is already in use.'])->withInput();
            }
            if ($e->validator->errors()->has('email')) {
                return back()->withErrors(['email' => 'This email is already in use.'])->withInput();
            }
        }
    }


    function edit(User $user)
    {
        return view('entity.users.edit', [
            'user' => $user,
            'departments' => Unit::all(),
        ]);
    }

    function update(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'nik' => ['required', Rule::unique('users')->ignore($user->id)],
                'name' => ['required'],
                'gender' => ['required'],
                'phone' => ['nullable'],
                'email' => ['nullable', Rule::unique('users')->ignore($user->id)],
                'role' => ['required'],
                'join_date' => ['nullable'],
                'address' => ['nullable'],
                'unit_id' => ['nullable'],
                'access_worklist' => ['nullable'],
                'access_control_budget' => ['nullable'],
                'access_payment_schedule' => ['nullable'],
            ]);

            // Hash password if provided
            if (!empty($request->password)) {
                $data['password'] = bcrypt($request->password);
            }

            $data['access_worklist'] = $request->has('access_worklist') ? 1 : 0;
            $data['access_control_budget'] = $request->has('access_control_budget') ? 1 : 0;
            $data['access_payment_schedule'] = $request->has('access_payment_schedule') ? 1 : 0;

            $user->update($data);
            return redirect('/user')->with('success', 'Employee has been successfully updated.');
        } catch (ValidationException $e) {
            if ($e->validator->errors()->has('nik')) {
                return back()->withErrors(['nik' => 'This NIK is already in use.'])->withInput();
            }
            if ($e->validator->errors()->has('email')) {
                return back()->withErrors(['email' => 'This email is already in use.'])->withInput();
            }
        }
    }

    function destroy(User $user)
    {
        // Cek apakah NIK digunakan di tabel DepartmenUser
        $cek_departmenUser = DepartmenUser::where('user_id', $user->id)->exists();
        if ($cek_departmenUser) {
            return back()->withErrors(['user_id' => 'This employee is currently assigned to a department employee.'])->withInput();
        }

        // Delete the user if no longer in use
        $user->delete();
        return back()->with('success', 'Employee has been successfully deleted.');
    }

    public function searchUser(Request $request)
    {
        $term = $request->get('term');
        $users = User::where('name', 'LIKE', '%' . $term . '%')->get();

        return response()->json($users);  // Return hasil pencarian sebagai JSON
    }
}
