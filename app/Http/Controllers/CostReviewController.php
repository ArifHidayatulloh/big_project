<?php

namespace App\Http\Controllers;

use App\Models\MonthlyBudget;
use App\Models\Actual;
use App\Models\BudgetCategory;
use App\Models\BudgetDescription;
use App\Models\BudgetDescriptionGrouping;
use App\Models\BudgetSubCategory;
use App\Models\Unit;
use App\Models\DepartmenUser;
use App\Models\CostReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostReviewController extends Controller
{

    // Manage cost review
    function index()
    {
        $units = Unit::all();
        $user_id = Auth::user()->id;
        $unit_id = Auth::user()->unit_id;
        $role = Auth::user()->role;

        if ($role == 1) {
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
            $costReview = CostReview::whereIn('unit_id', $departmentIds)->with('unit')->get();
            return view('control_budget.cost_review.index', [
                'costReviews' => $costReview,
                'units' => $units
            ]);
        } elseif ($role == 2) {
            $costReview = CostReview::with('unit')->get();
            return view('control_budget.cost_review.index', [
                'costReviews' => $costReview,
                'units' => $units
            ]);
        } elseif ($role == 3) {
            // Ambil unit ACCOUNTING
            $unit_accounting = Unit::where('name', 'ACCOUNTING')->first();

            // Ambil unit_id yang terkait dengan user
            $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');

            if ($unit_accounting && $departmentIds->contains($unit_accounting->id)) {
                // Jika user memiliki unit ACCOUNTING, tampilkan semua CostReview
                $costReview = CostReview::with('unit')->get();
            } else {
                // Jika tidak, hanya tampilkan CostReview berdasarkan unit yang dimiliki user
                $costReview = CostReview::whereIn('unit_id', $departmentIds)->with('unit')->get();
            }

            return view('control_budget.cost_review.index', [
                'costReviews' => $costReview,
                'units' => $units
            ]);
        } elseif ($role == 4) {
            if ($unit_id === null) {
                // Jika unit_id tidak tersedia, ambil berdasarkan DepartmenUser
                $departmentIds = DepartmenUser::where('user_id', $user_id)->pluck('unit_id');
                $costReview = CostReview::whereIn('unit_id', $departmentIds)->with('unit')->get();
            } else {
                // Jika unit_id tersedia, gunakan unit_id untuk filter
                $costReview = CostReview::where('unit_id', $unit_id)->with('unit')->get();
            }

            return view('control_budget.cost_review.index', [
                'costReviews' => $costReview,
                'units' => $units
            ]);
        } else {
            // Perbaikan untuk role 5
            $costReview = CostReview::where('unit_id', $unit_id)->with('unit')->get();
            return view('control_budget.cost_review.index', [
                'costReviews' => $costReview,
                'units' => $units
            ]);
        }
    }

    function store_cost_review(Request $request)
    {
        $request->validate([
            'unit_id' => 'required',
            'review_name' => 'required',
        ]);

        $cek = CostReview::where('unit_id', $request->unit_id)
            ->where('review_name', $request->review_name)
            ->first();

        if ($cek) {
            return redirect()->back()->withErrors(['error' => 'Cost review already exists.']);
        } else {
            CostReview::create($request->all());
            return redirect()->back()->with('success', 'Cost review has been successfully added.');
        }
    }

    function update_cost_review(Request $request, $id)
    {
        $cost_review = CostReview::find($id);

        if (!$cost_review) {
            return redirect()->back()->withErrors(['error' => 'Cost review not found.']);
        }

        $request->validate([
            'unit_id' => 'required',
            'review_name' => 'required',
        ]);

        $cek = CostReview::where('unit_id', $request->unit_id)
            ->where('review_name', $request->review_name)
            ->where('id', '!=', $id)
            ->first();

        if ($cek) {
            return redirect()->back()->withErrors(['error' => 'Cost review already exists.']);
        }

        $cost_review->update($request->all());

        return redirect()->back()->with('success', 'Cost review has been successfully updated.');
    }

    function destroy_cost_review($id)
    {
        $costReview = CostReview::find($id);
        $costReview->delete();
        return redirect()->back()->with('success', 'Cost review has been successfully deleted.');
    }
    // End of manage cost review

    // Show cost review of unit
    function show(Request $request, $id)
    {
        $costReview = CostReview::findOrFail($id);

        $selectedYear = $request->query('year', date('Y'));
        $selectedMonth = $request->query('month', date('F'));

        $descriptions = BudgetDescription::with(['monthly_budget' => function ($query) use ($selectedYear, $selectedMonth) {
            $query->where('year', $selectedYear)
                ->where('month', $selectedMonth);
        }])
            ->where('cost_review_id', $costReview->id)
            ->whereHas('monthly_budget', function ($query) use ($selectedYear, $selectedMonth) {
                $query->where('year', $selectedYear)
                    ->where('month', $selectedMonth);
            })
            ->get();

        $years = [
            '2024',
            '2025',
            '2026',
            '2027',
            '2028',
            '2029',
        ];

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        $hasDataForSelectedMonth = $descriptions->isNotEmpty();

        return view('control_budget.cost_review.review_cost', compact('costReview', 'descriptions', 'years', 'months', 'selectedMonth', 'selectedYear', 'hasDataForSelectedMonth'));
    }

    public function show_period(Request $request, $id)
    {
        $years = [
            '2024',
            '2025',
            '2026',
            '2027',
            '2028',
            '2029',
        ];

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        $selectedMonths = $request->input('months', []);
        $selectedYear = $request->input('years', date('Y'));

        if (empty($selectedMonths)) {
            $selectedMonths = [date('F')];
        }

        $costReview = CostReview::find($id);

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
            $hasMonthlyBudget = false; // Flag untuk cek monthly_budget

            foreach ($description->monthly_budget as $monthlyBudget) {
                $hasMonthlyBudget = true; // Ada data monthly_budget
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
                'has_monthly_budget' => $hasMonthlyBudget, // Tambahkan flag
            ];
        });


        return view('control_budget.cost_review.review_period', [
            'costReview' => $costReview,
            'descriptions' => $processedDescriptions,
            'selectedMonths' => $selectedMonths,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'months' => $months
        ]);
    }

    public function show_consolidated(Request $request)
    {
        $years = [
            '2024',
            '2025',
            '2026',
            '2027',
            '2028',
            '2029',
        ];

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        $selectedMonths = $request->input('months', []);
        $selectedYear = $request->input('years', date('Y'));

        if (empty($selectedMonths)) {
            $selectedMonths = [date('F')];
        }

        // Ambil description_groups beserta deskripsi dan monthly_budget terkait
        $description_groups = BudgetDescriptionGrouping::with([
            'subcategory.category',
            'descriptions' => function ($query) use ($selectedYear, $selectedMonths) {
                $query->with([
                    'monthly_budget' => function ($query) use ($selectedYear, $selectedMonths) {
                        $query->where('year', $selectedYear)
                            ->whereIn('month', $selectedMonths);
                    },
                    'monthly_budget.actual' => function ($query) {
                        $query->select('actual_spent', 'monthly_budget_id');
                    }
                ]);
            }
        ])->get();

        // Proses deskripsi untuk tiap group
        $processedDescriptions = $description_groups->map(function ($description_group) use ($selectedMonths) {
            $totalPlannedBudget = 0;
            $totalActualSpent = 0;
            $remarks = '-';
            $hasMonthlyBudget = false; // Flag untuk cek monthly_budget

            // Pastikan menggunakan plural 'descriptions'
            foreach ($description_group->descriptions as $description) {
                foreach ($description->monthly_budget as $monthlyBudget) {
                    $hasMonthlyBudget = true; // Ada data monthly_budget
                    $totalPlannedBudget += $monthlyBudget->planned_budget ?? 0;

                    foreach ($monthlyBudget->actual as $actual) {
                        $totalActualSpent += $actual->actual_spent ?? 0;
                        $remarks = $actual->remark ?? '-';
                    }
                }
            }

            $variance = $totalPlannedBudget - $totalActualSpent;
            $percentage = $totalPlannedBudget > 0 ? ($totalActualSpent / $totalPlannedBudget) * 100 : 0;

            return [
                'category' => $description_group->subcategory->category->category_name ?? 'N/A',
                'subcategory' => $description_group->subcategory->sub_category_name ?? 'N/A',
                'subcategory_id' => $description_group->subcategory->id ?? null, // Tambahkan subcategory_id
                'total_planned_budget' => $totalPlannedBudget,
                'total_actual_spent' => $totalActualSpent,
                'variance' => $variance,
                'percentage' => $percentage,
                'remarks' => $remarks,
                'has_monthly_budget' => $hasMonthlyBudget,
                'description_group' => $description_group->name,
                'category_id' => $description_group->subcategory->category->id, // Tetap simpan category_id jika diperlukan
            ];

        });


        $processedDescriptions = $processedDescriptions->sortBy([
            ['category_id', 'asc'], // Urutkan berdasarkan category_id
            ['subcategory_id', 'asc'] // Lalu berdasarkan subcategory_id
        ]);


        return view('control_budget.cost_review.consolidation', [
            'description_groups' => $processedDescriptions,
            'selectedMonths' => $selectedMonths,
            'selectedYear' => $selectedYear,
            'years' => $years,
            'months' => $months
        ]);
    }




    // End of show cost review of unit

    // Manage Category
    function index_category()
    {
        $categories = BudgetCategory::all();
        $sub_categories = BudgetSubCategory::all();
        return view('control_budget.category.index', compact('categories', 'sub_categories'));
    }

    function store_category(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);

        $existingCategory = BudgetCategory::where('category_name', $request->category_name)->first();
        if ($existingCategory) {
            return redirect()->back()->withErrors(['error' => 'Category already exists.']);
        }

        BudgetCategory::create($request->all());

        return redirect()->back()->with('success', 'Category has been successfully added.');
    }

    function update_category(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);

        $category = BudgetCategory::find($id);

        if (!$category) {
            return redirect()->back()->withErrors(['error' => 'Category not found.']);
        }

        $existingCategory = BudgetCategory::where('category_name', $request->category_name)->where('id', '!=', $id)->first();
        if ($existingCategory) {
            return redirect()->back()->withErrors(['error' => 'Category already exists.']);
        }

        $category->update($request->all());

        return redirect()->back()->with('success', 'Category has been successfully updated.');
    }

    function destroy_category($id)
    {
        $category = BudgetCategory::find($id);
        $category->delete();
        return redirect()->back()->with('success', 'Category has been successfully deleted.');
    }

    function store_subcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_name' => 'required|string',
        ]);

        $existingSubcategory = BudgetSubCategory::where('category_id', $request->category_id)
            ->where('sub_category_name', $request->sub_category_name)
            ->first();
        if ($existingSubcategory) {
            return redirect()->back()->withErrors(['error' => 'Subcategory already exists.']);
        }

        BudgetSubCategory::create($request->all());

        return redirect()->back()->with('success', 'Subcategory has been successfully added.');
    }

    function update_subcategory(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_name' => 'required|string',
        ]);

        $subcategory = BudgetSubCategory::find($id);

        if (!$subcategory) {
            return redirect()->back()->withErrors(['error' => 'Subcategory not found.']);
        }

        $existingSubcategory = BudgetSubCategory::where('category_id', $request->category_id)
            ->where('sub_category_name', $request->sub_category_name)
            ->where('id', '!=', $id)
            ->first();
        if ($existingSubcategory) {
            return redirect()->back()->withErrors(['error' => 'Subcategory already exists.']);
        }

        $subcategory->update($request->all());

        return redirect()->back()->with('success', 'Subcategory has been successfully updated.');
    }

    function destroy_subcategory($id)
    {
        $subcategory = BudgetSubCategory::find($id);
        $subcategory->delete();
        return redirect()->back()->with('success', 'Subcategory has been successfully deleted.');
    }

    public function store_description_group(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string',
        ]);

        $existingDescriptionGroup = BudgetDescriptionGrouping::where('sub_category_id', $request->sub_category_id)
            ->where('name', $request->description_grouping_name)
            ->first();
        if ($existingDescriptionGroup) {
            return redirect()->back()->withErrors(['error' => 'Description group already exists.']);
        }

        BudgetDescriptionGrouping::create($request->all());
        return redirect()->back()->with('success', 'Description group has been successfully added.');
    }

    function update_description_group(Request $request, $id)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string',
        ]);

        $descriptionGroup = BudgetDescriptionGrouping::find($id);

        if (!$descriptionGroup) {
            return redirect()->back()->withErrors(['error' => 'Description group not found.']);
        }

        $existingDescriptionGroup = BudgetDescriptionGrouping::where('sub_category_id', $request->sub_category_id)
            ->where('name', $request->description_grouping_name)
            ->where('id', '!=', $id)
            ->first();
        if ($existingDescriptionGroup) {
            return redirect()->back()->withErrors(['error' => 'Description group already exists.']);
        }

        $descriptionGroup->update($request->all());
        return redirect()->back()->with('success', 'Description group has been successfully updated.');
    }

    function destroy_description_group($id)
    {
        $descriptionGroup = BudgetDescriptionGrouping::find($id);
        $descriptionGroup->delete();
        return redirect()->back()->with('success', 'Description group has been successfully deleted.');
    }


    function index_description($id)
    {
        $costReview = CostReview::find($id);
        $subcategories = BudgetSubCategory::all();
        $groupings = BudgetDescriptionGrouping::all();
        $descriptions = BudgetDescription::with(['subcategory.category', 'grouping'])->where('cost_review_id', $costReview->id)->get();
        return view('control_budget.category.description', compact('descriptions', 'subcategories', 'groupings', 'costReview'));
    }

    function store_description(Request $request)
    {
        $request->validate([
            'cost_review_id' => 'required|exists:cost_reviews,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'description_grouping_id' => 'required|exists:description_grouping,id',
            'description_text' => 'required|string',
        ]);

        $existingDescription = BudgetDescription::where('cost_review_id', $request->cost_review_id)
            ->where('description_text', $request->description_text)
            ->first();
        if ($existingDescription) {
            return redirect()->back()->withErrors(['error' => 'Description already exists.']);
        }

        BudgetDescription::create($request->all());
        return redirect()->back()->with('success', 'Description has been successfully added.');
    }

    function update_description(Request $request, $id)
    {
        $request->validate([
            'cost_review_id' => 'required|exists:cost_reviews,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'description_grouping_id' => 'required|exists:description_grouping,id',
            'description_text' => 'required|string',
        ]);

        $description = BudgetDescription::find($id);

        if (!$description) {
            return redirect()->back()->withErrors(['error' => 'Description not found.']);
        }

        $existingDescription = BudgetDescription::where('cost_review_id', $request->cost_review_id)
            ->where('description_text', $request->description_text)
            ->where('id', '!=', $id)
            ->first();

        if ($existingDescription) {
            return redirect()->back()->withErrors(['error' => 'Description already exists.']);
        }

        $description->update($request->all());
        return redirect()->back()->with('success', 'Description has been successfully updated.');
    }

    function destroy_description($id)
    {
        $description = BudgetDescription::find($id);
        $description->delete();
        return redirect()->back()->with('success', 'Description has been successfully deleted.');
    }
    // End of manage Category

    // Monthly budget
    function index_monthly_budget($id)
    {
        $costReview = CostReview::find($id);
        $descriptions = BudgetDescription::with(['subcategory.category'])->where('cost_review_id', $costReview->id)->orderBy('sub_category_id', 'asc')->get();
        $years = range(date('Y'), date('Y') + 5);
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $desctiptions =  $descriptions->sortBy([
            ['subcategory_id', 'asc'] // Lalu berdasarkan subcategory_id
        ]);
        return view('control_budget.monthly_budget.planned_budget', compact('descriptions', 'costReview', 'years', 'months'));
    }

    public function store_budget(Request $request)
    {
        $validated = $request->validate([
            'cost_review_id' => 'required|integer',
            'year' => 'required|integer',
            'months' => 'required|array',
            'planned_budget' => 'required|array',
        ]);

        // Bersihkan dan validasi format planned_budget
        $plannedBudget = collect($request->input('planned_budget'))->map(function ($amount) {
            // Hapus semua karakter kecuali angka dan koma
            $amount = preg_replace('/[^0-9,]/', '', $amount);

            // Ganti koma dengan titik untuk decimal (format SQL)
            $amount = str_replace(',', '.', $amount);

            // Jika nilai kosong setelah proses, ubah menjadi 0
            return $amount !== '' ? $amount : '0';
        });

        foreach ($request->months as $month) {
            // Iterasi untuk menyimpan atau memperbarui data
            foreach ($plannedBudget as $descriptionId => $amount) {
                MonthlyBudget::updateOrCreate(
                    [
                        'cost_review_id' => $request->input('cost_review_id'),
                        'description_id' => $descriptionId,
                        'year' => $request->input('year'),
                        'month' => $month,
                    ],
                    [
                        'planned_budget' => $amount, // Data decimal sudah bersih
                    ]
                );
            }
        }

        return redirect('/cost-review/' . $request->cost_review_id)
            ->with('success', 'Budget has been saved successfully.');
    }

    private function getMonthNumber($monthName)
    {
        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        return $months[$monthName] ?? null; // Jika nama bulan tidak ditemukan, kembalikan null
    }

    function edit_budget($cost_review, $month, $year)
    {
        $monthNumber = $this->getMonthNumber($month);

        if (!$monthNumber) {
            abort(400, 'Invalid month provided');
        }

        $costReview = CostReview::find($cost_review);
        if (!$costReview) {
            abort(400, 'Cost review not found');
        }

        $descriptions = BudgetDescription::with(['monthly_budget'])
            ->where('cost_review_id', $costReview->id)
            ->get();

        $descriptions->each(function ($description) use ($year, $monthNumber) {
            $description->monthly_budget
                ->where('year', $year)
                ->where('month', $monthNumber);
        });


        return view('control_budget.monthly_budget.edit_budget', compact('costReview', 'monthNumber', 'year', 'descriptions'));
    }

    function update_budget(Request $request)
    {
        $request->validate([
            'cost_review_id' => 'required|exists:cost_reviews,id',
            'year' => 'required',
            'month' => 'required',
            'planned_budget' => 'required|array',
            'planned_budget.*' => 'nullable|string',
        ]);

        $costReview = CostReview::findOrFail($request->cost_review_id);

        foreach ($request->planned_budget as $descriptionId => $plannedBudget) {
            $cleanBudget = preg_replace('/[^0-9]/', '', $plannedBudget);

            MonthlyBudget::updateOrCreate(
                [
                    'cost_review_id' => $costReview->id,
                    'description_id' => $descriptionId,
                    'year' => $request->year,
                    'month' => $request->month,
                ],
                [
                    'planned_budget' => $cleanBudget,
                ]
            );
        }

        return redirect('/cost-review/' . $request->cost_review_id)->with('success', 'Budget has been updated successfully.');
    }
    // End of budget

    // Actual
    private function applySorting($query, Request $request)
    {
        $sortColumn = $request->input('sort_column', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        return $query->orderBy($sortColumn, $sortOrder);
    }

    function index_actual(Request $request, $id)
    {
        $monthlyBudget = MonthlyBudget::with([
            'actual' => function ($query) use ($request) {
                $this->applySorting($query, $request);
            },
            'description.subcategory',
            'cost_review'
        ])->findOrFail($id);

        $actuals = $monthlyBudget->actual;

        $costReviewId = $monthlyBudget->cost_review->id;

        $totalSpent = $actuals->sum('actual_spent');

        return view('control_budget.actual.detail', compact('monthlyBudget', 'costReviewId', 'actuals', 'totalSpent'));
    }

    function store_actual(Request $request)
    {
        $request->validate([
            'monthly_budget_id' => 'required|exists:monthly_budgets,id',
            'date' => 'required',
            'no_source' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'ribuan' => 'required|min:0',
            'desimal' => 'required|min:0',
        ]);

        $ribuan = str_replace('.', '', $request->input('ribuan'));
        $desimal = $request->input('desimal');

        $actualSpent = $ribuan . ',' . $desimal;

        $actualSpentNumeric = floatval(str_replace(',', '.', $actualSpent));

        $monthlyBudget = MonthlyBudget::with('actual')->findOrFail($request->monthly_budget_id);

        // Hitung total pengeluaran saat ini (actual spent) dan anggaran yang direncanakan
        $totalSpent = $monthlyBudget->actual->sum('actual_spent');
        $remainingBudget = $monthlyBudget->planned_budget - $totalSpent;

        // Validasi apakah actual spent melebihi anggaran yang tersisa
        if ($actualSpentNumeric > $remainingBudget) {
            return redirect()->back()->withErrors([
                'actual_spent' => 'The actual spent exceeds the remaining budget. Please adjust the amount.',
            ])->withInput();
        }

        // Simpan data actual
        Actual::create([
            'monthly_budget_id' => $request->monthly_budget_id,
            'date' => $request->date,
            'no_source' => $request->no_source,
            'description' => $request->description,
            'actual_spent' => $actualSpentNumeric,
        ]);

        return redirect()->back()->with('success', 'Actual has been successfully planned.');
    }

    function update_actual(Request $request, $id)
    {
        $actual = Actual::findOrFail($id);

        // Validasi input
        $request->validate([
            'monthly_budget_id' => 'required|exists:monthly_budgets,id',
            'date' => 'required|date',
            'no_source' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'ribuan' => 'required',
            'desimal' => 'required',
        ]);

        // Mengambil nilai input ribuan dan desimal
        $ribuan = str_replace('.', '', $request->input('ribuan'));
        $desimal = $request->input('desimal');

        // Menggabungkan ribuan dan desimal untuk membentuk angka desimal
        $actualSpent = $ribuan . ',' . $desimal;

        // Konversi ke format numerik (float) untuk perhitungan
        $actualSpentNumeric = floatval(str_replace(',', '.', $actualSpent));

        // Ambil data MonthlyBudget terkait
        $monthlyBudget = MonthlyBudget::with('actual')->findOrFail($request->monthly_budget_id);

        // Hitung total pengeluaran saat ini (actual spent)
        $totalSpent = $monthlyBudget->actual->where('id', '!=', $id)->sum('actual_spent'); // Kecualikan pengeluaran yang sedang diubah
        $remainingBudget = $monthlyBudget->planned_budget - $totalSpent;

        // Validasi apakah actual spent melebihi anggaran yang tersisa
        if ($actualSpentNumeric > $remainingBudget) {
            return redirect()->back()->withErrors([
                'actual_spent' => 'The actual spent exceeds the remaining budget. Please adjust the amount.',
            ])->withInput();
        }

        // Perbarui data actual
        $actual->update([
            'monthly_budget_id' => $request->monthly_budget_id,
            'date' => $request->date,
            'source' => $request->source ?? $actual->source, // Tambahkan fallback jika `source` tidak ada
            'no_source' => $request->no_source,
            'description' => $request->description,
            'actual_spent' => $actualSpentNumeric,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Actual has been successfully updated.');
    }

    function destroy_actual($id)
    {
        $actual = Actual::findOrFail($id);
        $actual->delete();
        return redirect()->back()->with('success', 'Actual has been successfully deleted.');
    }

    // End of Actual

}
