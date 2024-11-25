<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentScheduleController extends Controller
{
    function index(Request $request)
    {
        $query = PaymentSchedule::query();

        // Filter by search (Invoice Number or Supplier Name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                    ->orWhere('supplier_name', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by purchase date range
        if ($purchaseDateFrom = $request->input('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $purchaseDateFrom);
        }

        if ($purchaseDateTo = $request->input('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $purchaseDateTo);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'purchase_date'); // Default sort column
        $sortOrder = $request->input('sort_order', 'asc');     // Default sort order
        $query->orderBy($sortBy, $sortOrder);

        // Paginate the result and keep query string parameters for pagination links
        $paymentSchedules = $query->paginate(10)->appends($request->all());

        return view('payment_schedule.index', compact('paymentSchedules'));
    }

    function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:payment_schedules,invoice_number',
            'supplier_name' => 'required|string|max:255',
            'payment_amount' => 'required',
            'purchase_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        $validatedData['payment_amount'] = (float) str_replace('.', '', $validatedData['payment_amount']);
        $validatedData['status'] = 'Unpaid';

        PaymentSchedule::create($validatedData);

        return redirect()->back()->with('success', 'Schedule has been successfully added.');
    }

    function update(Request $request, PaymentSchedule $paymentSchedule)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:payment_schedules,invoice_number,' . $paymentSchedule->id,
            'supplier_name' => 'required|string|max:255',
            'payment_amount' => 'required',
            'purchase_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        // Konversi payment_amount menjadi float dan hilangkan titik jika ada
        $validatedData['payment_amount'] = (float) str_replace('.', '', $validatedData['payment_amount']);

        // Update data paymentSchedule
        $paymentSchedule->update($validatedData);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Schedule has been successfully updated.');
    }

    function destroy(PaymentSchedule $paymentSchedule)
    {
        if ($paymentSchedule->attachment && Storage::exists('public/' . $paymentSchedule->attachment)) {
            Storage::delete('public/' . $paymentSchedule->attachment);
        }

        // Hapus data paymentSchedule
        $paymentSchedule->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Schedule has been successfully deleted.');
    }

    function edit(Request $request, $id)
    {
        $paymentSchedule = PaymentSchedule::findOrFail($id);

        // Tentukan aturan validasi dasar
        $rules = [
            'paid_date' => 'required',
            'description' => 'nullable',
        ];

        // Cek apakah ada file attachment
        if ($paymentSchedule->attachment != null) {
            // Jika ada attachment, maka file attachment opsional
            $rules['attachment'] = 'nullable|mimes:pdf|max:2048';
        } else {
            // Jika tidak ada attachment, maka attachment menjadi diperlukan
            $rules['attachment'] = 'required|mimes:pdf|max:2048';
        }

        // Validasi request menggunakan aturan yang telah ditentukan
        $request->validate($rules);


        $paymentSchedule->paid_date = $request->paid_date;
        $paymentSchedule->status = 'Paid';
        $paymentSchedule->description = $request->description;

        if ($request->hasFile('attachment')) {
            if ($paymentSchedule->attachment && Storage::exists('public/' . $paymentSchedule->attachment)) {
                Storage::delete('public/' . $paymentSchedule->attachment);
            }
            // Ambil file yang di-upload
            $file = $request->file('attachment');

            // Tentukan nama file berdasarkan invoice_number dan ekstensi file
            $attachmentFileName = $paymentSchedule->invoice_number . '.' . $file->getClientOriginalExtension();

            // Simpan file di folder yang ditentukan
            $filePath = $file->storeAs('attachment_payment_supplier', $attachmentFileName, 'public');

            // Update path file di database
            $paymentSchedule->attachment = $filePath;
        }

        $paymentSchedule->save();

        return redirect('/payment_schedule')->with('success', 'Payment has been successfully marked as paid.');
    }

    function rollback($id){
        $paymentSchedule = PaymentSchedule::findOrFail($id);
        $paymentSchedule->status = 'Unpaid';
        $paymentSchedule->paid_date = null;
        $paymentSchedule->description = null;
        if ($paymentSchedule->attachment && Storage::exists('public/' . $paymentSchedule->attachment)) {
            Storage::delete('public/' . $paymentSchedule->attachment);
        }
        $paymentSchedule->attachment = null;
        $paymentSchedule->save();

        return redirect('/payment_schedule')->with('success', 'Payment has been successfully rolled back to unpaid.');
    }
}
