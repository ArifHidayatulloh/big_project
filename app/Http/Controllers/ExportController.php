<?php

namespace App\Http\Controllers;

use App\Exports\CostReview;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkListExcel;
use App\Exports\PaymentSupplierExcel;
use App\Models\BudgetDescription;
use App\Models\CostReview as ModelsCostReview;
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
    function payment_supplier(Request $request)
    {
        $filters = $request->only(['search', 'status', 'purchase_date_from', 'purchase_date_to', 'due_date', 'startDate', 'endDate']);

        return Excel::download(new PaymentSupplierExcel($filters), 'payment_supplier.xlsx');
    }
    // End of Payment Supplier

    // Cost Review
    public function cost_review(Request $request)
    {
        $selectedMonths = $request->input('months', []);
        $selectedYear = $request->input('years', date('Y'));

        $costReviewId = $request->input('cost_review_id');
        $costReview = ModelsCostReview::findOrFail($costReviewId);

        $descriptions = BudgetDescription::with(['subcategory.category', 'monthly_budget' => function ($query) use ($selectedYear, $selectedMonths) {
            $query->where('year', $selectedYear)
                ->whereIn('month', $selectedMonths);
        }, 'monthly_budget.actual' => function ($query) {
            $query->select('actual_spent', 'monthly_budget_id');
        }])
            ->where('cost_review_id', $costReview->id)
            ->get();

        $processedDescriptions = $descriptions->map(function ($description) use ($selectedMonths) {
            $totalPlannedBudget = 0;
            $totalActualSpent = 0;
            $remarks = '-';

            foreach ($description->monthly_budget as $monthlyBudget) {
                $totalPlannedBudget += $monthlyBudget->planned_budget ?? 0;

                foreach ($monthlyBudget->actual as $actual) {
                    $totalActualSpent += $actual->actual_spent ?? 0;
                    $remarks = $actual->remark ?? '-';
                }
            }

            $variance = $totalPlannedBudget - $totalActualSpent;
            $percentage = $totalPlannedBudget > 0 ? ($totalActualSpent / $totalPlannedBudget) * 100 : 0;

            return [
                'description' => $description->description_text,
                'planned_budget' => $totalPlannedBudget,
                'actual_spent' => $totalActualSpent,
                'variance' => $variance,
                'percentage' => $percentage,
                'remarks' => $remarks,
                'category' => $description->subcategory->category->category_name ?? 'N/A',
                'subcategory' => $description->subcategory->sub_category_name ?? 'N/A',
            ];
        });


        // Export using view
        return Excel::download(new CostReview($processedDescriptions), 'cost-review.xlsx');
    }
    // End of Cost Review
}
