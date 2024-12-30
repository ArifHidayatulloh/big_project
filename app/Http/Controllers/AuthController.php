<?php

namespace App\Http\Controllers;

use App\Models\CostReview;
use App\Models\DepartmenUser;
use App\Models\PaymentSchedule;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\WorkingList;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');

        // Menambahkan pengecekan 'remember'
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Jika login berhasil
            return redirect()->intended('/');
        }

        // Jika login gagal
        return redirect()->back()->withErrors([
            'nik' => 'NIK atau password salah.',
        ])->withInput();
    }

    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect('/');
    }

    function dashboard()
    {
        $departments = Unit::all();
        $employees = User::all();
        $costReviews = CostReview::all();
        $paymentSchedules = PaymentSchedule::all();
        $role = Auth::user()->role;
        $user_id = Auth::user()->id;

        // Initialize working_lists query
        $working_lists = WorkingList::query();

        // Filter working_lists berdasarkan role
        if (in_array($role, [1, 3])) {
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
            if ($departmentIds->isNotEmpty()) {
                $working_lists->whereIn('unit_id', $departmentIds);
            }
        } elseif ($role == 2) {
            // Akses penuh (tidak perlu filter tambahan)
        } elseif ($role == 4) {
            $unit_id = Auth::user()->unit_id;
            if ($unit_id) {
                $working_lists->where('unit_id', $unit_id);
            } else {
                $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
                if ($departmentIds->isNotEmpty()) {
                    $working_lists->whereIn('unit_id', $departmentIds);
                }
            }
        } else {
            $working_lists->where('pic', $user_id);
        }

        // Statistik working_lists
        $workingListTotal = $working_lists->count();
        $workingListDone = (clone $working_lists)->where('status', 'Done')->count();
        $workingListOnProgress = (clone $working_lists)->where('status', 'On Progress')->count();
        $workingListOverdue = (clone $working_lists)->where('status', 'Overdue')->count();

        // Data untuk ditampilkan (7 hari ke depan)
        $workingLists = (clone $working_lists)->where('status', '!=', 'Done')
            ->where('status', '!=', 'Outstanding')
            ->whereBetween('deadline', [now(), now()->addDays(7)])
            ->orderBy('deadline', 'asc')
            ->get();

        return view('dashboard', compact(
            'departments',
            'employees',
            'costReviews',
            'paymentSchedules',
            'workingListTotal',
            'workingListDone',
            'workingListOnProgress',
            'workingListOverdue',
            'workingLists'
        ));
    }


    function edit_profile(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $data = $request->validate([
                'nik' => ['required', Rule::unique('users')->ignore($user)],
                'name' => ['required'],
                'gender' => ['required'],
                'phone' => ['nullable'],
                'email' => ['nullable', Rule::unique('users')->ignore($user)],
                'join_date' => ['nullable'],
                'address' => ['nullable'],
            ]);

            // Hash password if provided
            if (!empty($request->password)) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);
            return redirect('/')->with('success', 'Profile has been successfully updated.');
        } catch (ValidationException $e) {
            if ($e->validator->errors()->has('nik')) {
                return back()->withErrors(['nik' => 'This NIK is already in use.'])->withInput();
            }
            if ($e->validator->errors()->has('email')) {
                return back()->withErrors(['email' => 'This email is already in use.'])->withInput();
            }
        }
    }
}
