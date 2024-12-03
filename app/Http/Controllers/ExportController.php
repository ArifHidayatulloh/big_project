<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkListExcel;
use App\Exports\PaymentSupplierExcel;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    // Working List
    function excel_working_list(Request $request)
    {
        $status = $request->input('status');
        $dep_code = $request->input('dep_code');
        $pic = $request->input('pic');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // Pastikan status tetap sebagai array
        $status = is_array($status) ? $status : explode(',', $status);

        // Jika tidak ada status yang dipilih, kita ambil semua status
        if (empty($status) || (count($status) === 1 && $status[0] === '')) {
            $status = ['Done', 'On Progress', 'Outstanding']; // Ganti dengan semua status yang valid
        }

        return Excel::download(new WorkListExcel($status, $dep_code, $pic, $from_date, $to_date), 'working_list.xlsx');
    }
    // End of Working List

    // Payment Supplier
    function payment_supplier(Request $request){
        $filters = $request->only(['search', 'status', 'purchase_date_from','purchase_date_to','due_date','startDate','endDate']);

        return Excel::download(new PaymentSupplierExcel($filters), 'payment_supplier.xlsx');
    }
    // End of Payment Supplier
}
