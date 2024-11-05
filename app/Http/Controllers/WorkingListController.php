<?php

namespace App\Http\Controllers;

use App\Mail\WorkingListMail;
use App\Mail\WorkingListApproved;
use App\Mail\WorkingListRejected;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;
use App\Models\WorkingList;
use App\Models\CommentDephead;
use App\Models\UpdatePic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class WorkingListController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkingList::query();

        // Filter by Department
        if ($request->has('department') && $request->department != '') {
            $query->where('unit_id', $request->department);
        }

        // Filter by PIC
        if ($request->has('pic') && $request->pic != '') {
            $query->where('pic', $request->pic);
        }

        // Filter by Status (allowing multiple statuses)
        if ($request->has('status') && is_array($request->status) && count($request->status) > 0) {
            $query->whereIn('status', $request->status);
        }

        // Filter by Date Range (from_date and to_date)
        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
        }

        // Fetch working lists with related data
        $workingLists = $query->with(['commentDepheads.updatePics', 'department', 'picUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        foreach ($workingLists as $list) {
            // Panggil metode updateStatusIfNeeded untuk memperbarui status jika perlu
            $list->updateStatusIfNeeded();

            // Jika status_comment sudah diisi di database, gunakan itu
            if (!empty($list->status_comment)) {
                continue; // Skip iterasi ini, karena status sudah ada
            }

            $hasUpdates = false; // Flag untuk cek apakah ada update

            // Iterasi melalui setiap CommentDephead
            foreach ($list->commentDepheads as $comment) {
                if ($comment->updatePics->isNotEmpty()) {
                    $hasUpdates = true;
                    break; // Jika sudah ada update, tidak perlu cek lebih lanjut
                }
            }

            // Tentukan status berdasarkan flag $hasUpdates
            if ($hasUpdates) {
                $list->status_comment = 'In progress';
            } else {
                $list->status_comment = 'No start';
            }

            if ($list->status == 'Done') {
                $list->status_comment = 'Completed';
            }
        }

        // Fetch filter options
        $departments = Unit::orderBy('name', 'asc')->get();
        $pics = User::orderBy('name', 'asc')->get();

        return view('working_list.index', compact('workingLists', 'departments', 'pics'));
    }

    function show($id)
    {
        $item = WorkingList::with(['creator', 'department', 'picUser', 'commentDepheads'])->findOrFail($id);
        return view('working_list.detail', compact('item'));
    }

    function create()
    {
        return view('working_list.create', [
            'departments' => Unit::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
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
                $status = 'Outstanding';
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
        // Jika status Outstanding dan status_comment 'finish'
        elseif ($status == 'Outstanding' && $validatedData['status_comment'] == 'completed') {
            $score = 85;
        }
        // Jika status Outstanding dan status_comment 'uncompleted'
        elseif ($status == 'Outstanding' && $validatedData['status_comment'] == 'uncompleted') {
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
        $workingList->status = 'Requested';
        $workingList->save();

        return redirect("/working-list/$workingList->id")->with('success', 'Request for approval has been sent. ');
    }

    function request_approve()
    {
        $workingLists = WorkingList::where('status', 'Requested')->paginate(20);

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

        $status = 'Outstanding';
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
            'status' => 'Rejected',
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
