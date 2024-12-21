<?php

namespace App\Http\Controllers;

use App\Mail\WorkingListMail;
use App\Mail\WorkingListApproved;
use App\Mail\WorkingListRejected;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;
use App\Models\DepartmenUser;
use App\Models\WorkingList;
use App\Models\CommentDephead;
use App\Models\UpdatePic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkingListController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $user_id = Auth::user()->id;
        $unit_id = Auth::user()->unit_id;

        $query = WorkingList::query();

        // Filter berdasarkan role
        $this->applyRoleFilter($query, $role, $user_id, $unit_id);

        // Tambahkan filter dari request
        $this->applyRequestFilters($query, $request);

        // Sorting
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $sortBy = $request->get('sort_by');
            $sortOrder = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';

            // Validasi dan sorting berdasarkan kolom yang diinginkan
            if (in_array($sortBy, ['deadline', 'score'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            // Default sorting berdasarkan tanggal dibuat
            $query->orderBy('created_at', 'desc');
        }

        // Ambil data dengan relasi
        $workingLists = $query->with(['commentDepheads.updatePics', 'department', 'picUser'])
            ->paginate(25)
            ->appends($request->all());

        // Perbarui status komentar
        $this->updateWorkingListStatuses($workingLists);

        // Data dropdown untuk filter
        $departments = $this->getAvailableDepartments($role, $user_id);
        $pics = $this->getAvailablePics($role, $user_id, $departments);

        return view('working_list.index', compact('workingLists', 'departments', 'pics'));
    }


    /**
     * Terapkan filter berdasarkan role pengguna.
     */
    private function applyRoleFilter($query, $role, $user_id, $unit_id)
    {
        if ($role == 1 || $role == 3 || $role == 4) {
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
            if ($departmentIds->isNotEmpty()) {
                $query->whereIn('unit_id', $departmentIds);
            }
            if ($role == 4 && $unit_id) {
                $query->where('unit_id', $unit_id);
            }
        } elseif ($role == 5) {
            $query->where('pic', $user_id);
        }
    }

    /**
     * Terapkan filter tambahan dari request.
     */
    private function applyRequestFilters($query, $request)
    {
        if ($request->filled('department')) {
            $query->where('unit_id', $request->department);
        }

        if ($request->filled('pic')) {
            $query->where('pic', $request->pic);
        }

        if ($request->filled('status')) {
            $query->whereIn('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }
    }

    /**
     * Perbarui status komentar pada daftar kerja.
     */
    private function updateWorkingListStatuses($workingLists)
    {
        foreach ($workingLists as $list) {
            // Jika status_comment sudah ada, lewati
            if (!empty($list->status_comment)) {
                continue;
            }

            // Tentukan status komentar berdasarkan kondisi
            $list->status_comment = $this->determineStatusComment($list);
        }
    }

    /**
     * Tentukan status komentar berdasarkan kondisi.
     */
    private function determineStatusComment($list)
    {
        if ($list->status == 'Done') {
            return 'Completed';
        }

        foreach ($list->commentDepheads as $comment) {
            if ($comment->updatePics->isNotEmpty()) {
                return 'In progress';
            }
        }

        return 'No start';
    }

    /**
     * Ambil daftar departemen yang tersedia untuk role tertentu.
     */
    private function getAvailableDepartments($role, $user_id)
    {
        if ($role == 1 || $role == 3 || $role == 4) {
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
            return Unit::whereIn('id', $departmentIds)->orderBy('name', 'asc')->get();
        }

        return Unit::orderBy('name', 'asc')->get();
    }

    /**
     * Ambil daftar PIC yang tersedia untuk role tertentu.
     */
    private function getAvailablePics($role, $user_id, $departments)
    {
        if ($role == 4) { // Kepala Unit
            $unitId = Auth::user()->unit_id;

            if ($unitId) {
                // Jika unit_id kepala unit tidak null, ambil user dengan role 5 dari unit yang sama
                return User::where('unit_id', $unitId)
                    ->where('role', 5)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                // Jika unit_id kepala unit null, ambil data terkait departemen
                $departmentIds = $departments->pluck('id');

                // Ambil data user terkait dengan unit
                $users = User::whereIn('unit_id', $departmentIds)
                    ->where('role', '!=', 1) // Kecualikan user dengan role 1
                    ->orderBy('name', 'asc')
                    ->get();

                // Ambil data dari DepartmenUser yang terkait dengan unit
                $depUsers = DepartmenUser::whereIn('unit_id', $departmentIds)
                    ->whereHas('user', function ($query) {
                        $query->where('role', '!=', 1); // Kecualikan user dengan role 1
                    })
                    ->with('user')
                    ->get()
                    ->pluck('user');

                // Gabungkan kedua koleksi
                $pics = $users->merge($depUsers)->unique('id')->sortBy('name');

                return $pics->values(); // Reset index setelah pengurutan
            }
        }

        if ($role == 1 || $role == 3) { // Admin atau Manajer
            $departmentIds = $departments->pluck('id');

            // Ambil data user terkait dengan unit
            $users = User::whereIn('unit_id', $departmentIds)
                ->where('role', '!=', 1) // Kecualikan user dengan role 1
                ->orderBy('name', 'asc')
                ->get();

            // Ambil data dari DepartmenUser yang terkait dengan unit
            $depUsers = DepartmenUser::whereIn('unit_id', $departmentIds)
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', 1); // Kecualikan user dengan role 1
                })
                ->with('user')
                ->get()
                ->pluck('user');

            // Gabungkan kedua koleksi
            $pics = $users->merge($depUsers)->unique('id')->sortBy('name');

            return $pics->values(); // Reset index setelah pengurutan
        }

        // Untuk role lainnya, tampilkan semua user kecuali role 1
        return User::where('role', '!=', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    function show($id)
    {
        $item = WorkingList::with(['creator', 'department', 'picUser', 'commentDepheads'])->findOrFail($id);
        return view('working_list.detail', compact('item'));
    }

    function create()
    {
        $role = Auth::user()->role;
        $user_id = Auth::user()->id;
        $departments = $this->getAvailableDepartments($role, $user_id);

        // Logika untuk mendapatkan daftar pengguna berdasarkan role
        if ($role == 1) {
            $departmentIds = $departments->pluck('id'); // Ambil ID departemen

            // Ambil semua pengguna dengan unit_id yang sesuai dan bukan role 1
            $pic = User::whereIn('unit_id', $departmentIds)
                ->where('role', '!=', 1) // Kecualikan pengguna dengan role 1
                ->orderBy('name', 'asc')
                ->get();

            // Ambil pengguna dari DepartmenUser dengan role bukan 1
            $depUsers = DepartmenUser::whereIn('unit_id', $departmentIds)
                ->whereHas('user', function ($query) {
                    $query->where('role', '!=', 1); // Kecualikan user dengan role 1
                })
                ->with('user')
                ->get()
                ->pluck('user');

            // Ambil pengguna dengan role = 2
            $role_2 = User::where('role', 2)
                ->where('name', '!=', 'ADMIN') // Pastikan bukan ADMIN
                ->get();

            // Gabungkan semua data
            $users = $pic->merge($depUsers)->merge($role_2)->unique('id')->sortBy('name');
        } else {
            $users = User::where('role', '!=', 1) // Ambil semua pengguna kecuali role 1
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('working_list.create', [
            'departments' => $departments,
            'users' => $users,
        ]);
    }

    function store(Request $request)
    {
        $validatedData = $request->validate([
            'department_id' => 'required|exists:units,id',
            'name' => 'required|string',
            'pic' => 'required|exists:users,id',
            'relatedpic' => 'nullable|array',
            'relatedpic.*' => 'exists:users,id',
            'deadline' => 'required|date',
            'is_priority' => 'nullable|boolean',
            'complete_date' => 'nullable|date',
            'comment_depheads' => 'required|array',
            'comment_depheads.*' => 'required|string',
        ]);

        // Simpan working list
        $workingList = WorkingList::create([
            'unit_id' => $validatedData['department_id'],
            'name' => $validatedData['name'],
            'pic' => $validatedData['pic'],
            'relatedpic' => isset($validatedData['relatedpic']) ? $validatedData['relatedpic'] : null,
            'deadline' => $validatedData['deadline'],
            'complete_date' => $validatedData['complete_date'],
            'is_priority' => $request->has('is_priority') ? 1 : 0,
            'status' => 'On Progress',
            'created_by' => auth()->id(),
        ]);

        // Simpan comment_depheads
        foreach ($request->comment_depheads as $comment) {
            CommentDephead::create([
                'working_list_id' => $workingList->id,
                'comment' => $comment,
            ]);
        }

        // Mengirim email ke pic
        $picUser = User::find($validatedData['pic']);
        if ($picUser->email != null) {
            Mail::to($picUser->email)->send(new WorkingListMail($workingList));
            return redirect('/working-list')->with('success', 'Working list has been successfully added.');
        }
        return redirect('/working-list')->with('success', 'Working list has been successfully added.');
    }

    public function edit($id)
    {
        $item = WorkingList::with(['commentDepheads'])->findOrFail($id);

        // Pastikan relatedpic adalah array
        if (is_string($item->relatedpic)) {
            $item->relatedpic = json_decode($item->relatedpic, true) ?? [];
        } else {
            $item->relatedpic = $item->relatedpic ?? [];
        }


        // Ambil semua pengguna
        $users = User::orderBy('name', 'asc')->get();

        return view('working_list.edit', [
            'item' => $item,
            'departments' => Unit::orderBy('name', 'asc')->get(),
            'users' => $users, // Kirimkan variabel users
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'department_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255',
            'pic' => 'required|exists:users,id',
            'relatedpic' => 'nullable|array',
            'relatedpic.*' => 'exists:users,id',
            'deadline' => 'required|date',
            'is_priority' => 'nullable|boolean',
            'complete_date' => 'nullable|date',
            'status_comment' => 'nullable|string|in:completed,uncompleted', // tambahkan validasi untuk status_comment
            'comment_depheads' => 'nullable|array',
            'comment_depheads.*' => 'required|string|max:1000',
        ]);

        // Temukan item WorkingList berdasarkan ID
        $workingList = WorkingList::findOrFail($id);

        // Tentukan status berdasarkan perbandingan complete_date dan deadline
        $status = 'On Progress';  // Default status jika complete_date tidak diinput
        if ($validatedData['complete_date']) {
            if ($validatedData['complete_date'] > $validatedData['deadline']) {
                $status = 'Overdue';
            } else {
                $status = 'Done';
            }
        }

        // Logika penentuan score
        $score = null; // Inisialisasi default score

        // Jika status Done dan tepat waktu
        if ($status == 'Done' && $validatedData['complete_date'] <= $validatedData['deadline']) {
            $score = 100;
        }
        // Jika status Overdue dan status_comment 'finish'
        elseif ($status == 'Overdue' && $validatedData['status_comment'] == 'completed') {
            $score = 85;
        }
        // Jika status Overdue dan status_comment 'uncompleted'
        elseif ($status == 'Overdue' && $validatedData['status_comment'] == 'uncompleted') {
            $score = 50;
        }

        // Update WorkingList
        $workingList->update([
            'unit_id' => $validatedData['department_id'],
            'name' => $validatedData['name'],
            'pic' => $validatedData['pic'],
            'relatedpic' => isset($validatedData['relatedpic']) ? $validatedData['relatedpic'] : null,
            'deadline' => $validatedData['deadline'],
            'complete_date' => $validatedData['complete_date'],
            'is_priority' => $request->has('is_priority') ? 1 : 0,
            'status' => $status,  // Update status sesuai dengan logika
            'status_comment' => isset($validatedData['status_comment']) ? $validatedData['status_comment'] : null,  // Update status comment
            'score' => $score,  // Simpan score yang sudah dihitung
            'updated_by' => auth()->id(),
        ]);

        // Proses update atau penambahan comment_depheads dan update pics
        $existingCommentIds = $workingList->commentDepheads()->pluck('id')->toArray();

        foreach ($validatedData['comment_depheads'] as $index => $commentText) {
            // Jika ada comment_dephead_id di input, lakukan update
            if (isset($request->comment_dephead_ids[$index])) {
                $commentDephead = CommentDephead::find($request->comment_dephead_ids[$index]);
                if ($commentDephead) {
                    $commentDephead->update([
                        'comment' => $commentText
                    ]);
                    // Hapus ID dari daftar existingCommentIds untuk mengetahui mana yang masih ada
                    if (($key = array_search($commentDephead->id, $existingCommentIds)) !== false) {
                        unset($existingCommentIds[$key]);
                    }
                }
            } else {
                // Jika tidak ada ID, buat comment dephead baru
                CommentDephead::create([
                    'working_list_id' => $workingList->id,
                    'comment' => $commentText
                ]);
            }
        }

        // Hapus comment_depheads yang sudah tidak ada di input (beserta update pics-nya)
        CommentDephead::whereIn('id', $existingCommentIds)->delete();

        // Redirect kembali ke halaman working list dengan pesan sukses
        return redirect("/working-list/{$workingList->id}")->with('success', 'Working list has been successfully updated.');
    }



    public function destroy($id)
    {
        $workingList = WorkingList::findOrFail($id);

        $workingList->delete();

        return redirect('/working-list')->with('success', 'Working list has been successfully deleted.');
    }

    function updatePIC($commentId)
    {
        $comment = CommentDephead::findOrFail($commentId);
        return view('working_list.update', compact('comment'));
    }

    function storeUpdate(Request $request, $commentId)
    {
        $request->validate([
            'update' => 'required',
            'pdf_file' => 'nullable|mimes:pdf|max:2048'
        ]);

        $comment = CommentDephead::findOrFail($commentId);

        $updatePic = new UpdatePic();
        $updatePic->comment_dephead_id = $comment->id;
        $updatePic->update = $request->update;
        $updatePic->updated_by = auth()->id();

        // Jika ada File PDF diupload
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filePath = $file->store('pdf_files', 'public');
            $updatePic->pdf_file = $filePath;
        }

        $updatePic->save();

        return redirect("/working-list/{$comment->working_list_id}")->with('success', 'Update PIC Success');
    }

    function editUpdatePIC($id)
    {
        $updatePic = UpdatePic::findOrFail($id);
        return view('working_list.edit_update', compact('updatePic'));
    }

    function storeEditUpdate(Request $request, $id)
    {
        $request->validate([
            'update' => 'required',
            'pdf_file' => 'nullable|mimes:pdf|max:2048',
        ]);

        $auth = auth()->id();

        $updatePic = UpdatePic::findOrFail($id);
        $updatePic->update = $request->update;
        $updatePic->updated_by = $auth;

        if ($request->hasFile('pdf_file')) {
            //Hapus file lama jika ada
            if ($updatePic->pdf_file && Storage::exists('public/' . $updatePic->pdf_file)) {
                Storage::delete('public/' . $updatePic->pdf_file);
            }

            $pdfFileName = $request->file('pdf_file')->store('pdf_files', 'public');
            $updatePic->pdf_file = $pdfFileName;
        }

        $updatePic->save();

        return redirect("/working-list/{$updatePic->commentDephead->working_list_id}")->with('success', 'Update PIC as been successfully updated.');
    }

    function deleteUpdatePIC($id)
    {
        $updatePic = UpdatePic::findOrFail($id);
        $workingListId = $updatePic->commentDephead->working_list_id;

        if ($updatePic->pdf_file && Storage::exists('public/' . $updatePic->pdf_file)) {
            Storage::delete('public/' . $updatePic->pdf_file);
        }

        $updatePic->delete();

        return redirect("/working-list/{$workingListId}")->with('success', 'Update PIC has been successfully deleted.');
    }


    function request($id)
    {
        $workingList = WorkingList::findOrFail($id);

        $workingList->request_at = Carbon::now();
        $workingList->request_status = 'Requested';
        $workingList->save();

        return redirect("/working-list/$workingList->id")->with('success', 'Request for approval has been sent. ');
    }

    function request_approve(Request $request)
    {
        $user_id = Auth::user()->id;
        $pengurus = Auth::user()->role == 1;
        $gm = Auth::user()->role == 2;
        if($gm){
            $workingLists = WorkingList::where('request_status', 'Requested')->paginate(20)->appends($request->all());
        }else{
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
            $workingLists = WorkingList::where('request_status', 'Requested')->whereIn('unit_id',$departmentIds)->paginate(20)->appends($request->all());
        }

        return view('working_list.request_page', compact('workingLists'));
    }
    function request_detail($id)
    {
        $item = WorkingList::with(['creator', 'department', 'picUser', 'commentDepheads'])->findOrFail($id);
        return view('working_list.request_detail', compact('item'));
    }

    function approve(Request $request, $id)
    {
        $workingList = WorkingList::findOrFail($id);
        $validatedData = $request->validate([
            'complete_date' => 'required',
            'status_comment' => 'required|string|in:completed,uncompleted'
        ]);

        $status = 'Overdue';
        $score = 50;

        if ($validatedData['complete_date'] <= $workingList->deadline) {
            $status = 'Done';
            $score = 100;
        } else if ($validatedData['complete_date'] > $workingList->deadline && $validatedData['status_comment'] == 'completed') {
            $score = 85;
        }

        $workingList->update([
            'complete_date' => $validatedData['complete_date'],
            'status' => $status,
            'score' => $score,
            'status_comment' => $validatedData['status_comment'], // Simpan status comment
            'request_status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);
        // Kirim email ke PIC
        $userEmail = $workingList->picUser->email;
        if ($userEmail != null) {
            Mail::to($userEmail)->send(new WorkingListApproved($workingList));
            return redirect("/need_approval")->with('success', 'Working list has been successfully approved.');
        } else {
            return redirect("/need_approval")->with('success', 'Working list has been successfully approved.');
        }
    }

    function reject(Request $request, $id)
    {
        $workingList = WorkingList::findOrFail($id);
        $validatedData = $request->validate([
            'reject_reason' => 'required'
        ]);

        $workingList->update([
            'reject_reason' => $validatedData['reject_reason'],
            'request_status' => 'Rejected',
            'rejected_by' => auth()->id()
        ]);

        $userEmail = $workingList->picUser->email;
        if ($userEmail != null) {
            Mail::to($userEmail)->send(new WorkingListRejected($workingList));
            return redirect("/need_approval")->with('success', 'Working list has been successfully rejected.');
        } else {
            return redirect("/need_approval")->with('success', 'Working list has been successfully rejected.');
        }
    }
}
