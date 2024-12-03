<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentSchedule::query();

        // Apply filters
        $this->filterPaymentSchedules($query, $request);

        // Sorting
        $this->applySorting($query, $request);

        // Paginate results
        $paymentSchedules = $query->paginate(10)->appends($request->all());

        return view('payment_schedule.index', compact('paymentSchedules'));
    }

    public function unpaid(Request $request)
    {
        $paymentSchedules = $this->filterByDueDate(PaymentSchedule::where('status', 'Unpaid'), $request);
        $paymentSchedules = $paymentSchedules->orderBy('due_date', 'desc')->get();

        return view('payment_schedule.unpaid_recap', compact('paymentSchedules'));
    }

    public function paid(Request $request)
    {
        $query = PaymentSchedule::where('status', 'Paid');

        // Apply filters
        $this->filterBySearch($query, $request);
        $this->filterByDateRange($query, $request);

        // Get filtered and sorted data
        $paymentSchedules = $query->orderBy('paid_date', 'desc')->get();

        return view('payment_schedule.paid_recap', compact('paymentSchedules'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validatePaymentSchedule($request);
        $validatedData['payment_amount'] = $this->sanitizeAmount($validatedData['payment_amount']);
        $validatedData['status'] = 'Unpaid';

        PaymentSchedule::create($validatedData);

        return redirect()->back()->with('success', 'Schedule has been successfully added.');
    }

    public function update(Request $request, PaymentSchedule $paymentSchedule)
    {
        $validatedData = $this->validatePaymentSchedule($request, $paymentSchedule->id);
        $validatedData['payment_amount'] = $this->sanitizeAmount($validatedData['payment_amount']);

        $paymentSchedule->update($validatedData);

        return redirect()->back()->with('success', 'Schedule has been successfully updated.');
    }

    public function destroy(PaymentSchedule $paymentSchedule)
    {
        $this->deleteAttachment($paymentSchedule);

        $paymentSchedule->delete();

        return redirect()->back()->with('success', 'Schedule has been successfully deleted.');
    }

    public function edit(Request $request, $id)
    {
        $paymentSchedule = PaymentSchedule::findOrFail($id);
        $request->validate($this->getEditValidationRules($paymentSchedule));
        $this->updatePaymentSchedule($paymentSchedule, $request);

        return redirect('/payment_schedule')->with('success', 'Payment has been successfully marked as paid.');
    }

    public function rollback($id)
    {
        $paymentSchedule = PaymentSchedule::findOrFail($id);
        $this->rollbackPaymentSchedule($paymentSchedule);

        return redirect('/payment_schedule')->with('success', 'Payment has been successfully rolled back to unpaid.');
    }

    // Helper Methods
    private function filterPaymentSchedules($query, Request $request)
    {
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                    ->orWhere('supplier_name', 'like', "%$search%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($purchaseDateFrom = $request->input('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $purchaseDateFrom);
        }

        if ($purchaseDateTo = $request->input('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $purchaseDateTo);
        }
    }

    private function applySorting($query, Request $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'asc');

        // Default sorting if no filter is applied
        if (!$request->has('due_date') && !$request->has('sort_by') && !$request->has('sort_order')) {
            $sortOrder = 'desc'; // Default to descending order when no filters or sorting is set
        }

        $query->orderBy($sortBy, $sortOrder);
    }

    private function filterByDueDate($query, Request $request)
    {
        $dueDate = $request->query('due_date');
        if ($dueDate) {
            $query->whereDate('due_date', '<=', $dueDate);
        }
        return $query;
    }

    private function filterBySearch($query, Request $request)
    {
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($query) use ($searchTerm) {
                $query->where('invoice_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('supplier_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
    }

    private function filterByDateRange($query, Request $request)
    {
        if ($request->has('startDate') && $request->startDate != '') {
            $query->whereDate('paid_date', '>=', $request->startDate);
        }
        if ($request->has('endDate') && $request->endDate != '') {
            $query->whereDate('paid_date', '<=', $request->endDate);
        }
    }

    private function validatePaymentSchedule(Request $request, $id = null)
    {
        $uniqueRule = 'unique:payment_schedules,invoice_number';
        if ($id) {
            $uniqueRule .= ',' . $id;
        }

        return $request->validate([
            'invoice_number' => "required|string|max:255|$uniqueRule",
            'supplier_name' => 'required|string|max:255',
            'payment_amount' => 'required',
            'purchase_date' => 'required|date',
            'due_date' => 'required|date',
        ]);
    }

    private function sanitizeAmount($amount)
    {
        return (float) str_replace('.', '', $amount);
    }

    private function deleteAttachment(PaymentSchedule $paymentSchedule)
    {
        if ($paymentSchedule->attachment && Storage::exists('public/' . $paymentSchedule->attachment)) {
            Storage::delete('public/' . $paymentSchedule->attachment);
        }
    }

    private function getEditValidationRules(PaymentSchedule $paymentSchedule)
    {
        return [
            'paid_date' => 'required',
            'description' => 'nullable',
            'attachment' => ($paymentSchedule->attachment)
                ? 'nullable|mimes:pdf|max:2048'
                : 'required|mimes:pdf|max:2048',
        ];
    }

    private function updatePaymentSchedule(PaymentSchedule $paymentSchedule, Request $request)
    {
        $paymentSchedule->fill([
            'paid_date' => $request->paid_date,
            'status' => 'Paid',
            'description' => $request->description,
        ]);

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($paymentSchedule);

            $file = $request->file('attachment');
            $filePath = $file->storeAs(
                'attachment_payment_supplier',
                $paymentSchedule->invoice_number . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            $paymentSchedule->attachment = $filePath;
        }

        $paymentSchedule->save();
    }

    private function rollbackPaymentSchedule(PaymentSchedule $paymentSchedule)
    {
        $paymentSchedule->update([
            'status' => 'Unpaid',
            'paid_date' => null,
            'description' => null,
            'attachment' => null,
        ]);

        $this->deleteAttachment($paymentSchedule);
    }
}
