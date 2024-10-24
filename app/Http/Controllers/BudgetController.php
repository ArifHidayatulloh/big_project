<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategory;
use App\Models\BudgetSubCategory;
use App\Models\BudgetDescription;
use App\Models\CostReview;
use App\Models\MonthlyBudget;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    function index()
    {
        $costReview = CostReview::with('unit')->get();
        $units = Unit::all();

        return view('control_budget.cost_review.index', [
            'costReviews' => $costReview,
            'units' => $units
        ]);
    }

    function store_cost_review(Request $request)
    {
        // Validasi input
        $request->validate([
            'unit_id' => 'required',
            'review_name' => 'required',
        ]);

        // Pengecekan apakah CostReview dengan unit_id dan review_name sudah ada
        $cek = CostReview::where('unit_id', $request->unit_id)
            ->where('review_name', $request->review_name)
            ->first();

        if ($cek) {
            // Jika sudah ada, redirect dengan pesan error
            return redirect('/control-budget')->withErrors(['error' => 'Cost review already exists.']);
        } else {
            // Jika belum ada, buat cost review baru
            CostReview::create($request->all());
            return redirect('/control-budget')->with('success', 'Cost review has been successfully added.');
        }
    }

    function update_cost_review(Request $request, $id)
    {
        // Cek apakah CostReview dengan ID yang diberikan ada
        $cost_review = CostReview::find($id);

        if (!$cost_review) {
            return redirect('/control-budget')->withErrors(['error' => 'Cost review not found.']);
        }

        // Validasi inputan
        $request->validate([
            'unit_id' => 'required',
            'review_name' => 'required',
        ]);

        // Pengecekan apakah CostReview dengan unit_id dan review_name sudah ada, kecuali yang sedang di-update
        $cek = CostReview::where('unit_id', $request->unit_id)
            ->where('review_name', $request->review_name)
            ->where('id', '!=', $id) // Mengecualikan yang sedang di-update
            ->first();

        if ($cek) {
            // Jika sudah ada, redirect dengan pesan error
            return redirect('/control-budget')->withErrors(['error' => 'Cost review already exists.']);
        }

        // Jika belum ada, update cost review
        $cost_review->update($request->all());

        return redirect('/control-budget')->with('success', 'Cost review has been successfully updated.');
    }

    function destroy_cost_review($id)
    {
        $costReview = CostReview::find($id);
        $costReview->delete();
        return redirect()->back()->with('success', 'Cost review has been successfully deleted.');
    }

    function show($id)
    {
        $costReview = CostReview::find($id);

        $categories = BudgetCategory::where('cost_review_id', $costReview->id)
            ->with(['subcategory.descriptions'])->get();

        // Membuat koleksi untuk menyimpan subkategori dari semua kategori
        $sub_categories = BudgetSubcategory::whereHas('category', function ($query) use ($costReview) {
            $query->where('cost_review_id', $costReview->id);
        })->get();

        return view('control_budget.cost_review.description', compact('categories', 'costReview', 'sub_categories'));
    }


    public function storeCategory(Request $request)
    {
        // Validasi input
        $request->validate([
            'category_name' => 'required|string|max:255',
            'cost_review_id' => 'required|exists:cost_reviews,id',
        ]);

        // Cek apakah category dengan nama yang sama sudah ada
        $existingCategory = BudgetCategory::where('cost_review_id', $request->cost_review_id)
            ->where('category_name', $request->category_name)
            ->first();

        if ($existingCategory) {
            return redirect()->back()->withErrors(['error' => 'Category already exists for this cost review.']);
        }

        // Tambah category baru
        BudgetCategory::create([
            'cost_review_id' => $request->cost_review_id,
            'category_name' => $request->category_name,
        ]);

        return redirect()->back()->with('success', 'Category added successfully.');
    }


    public function storeSubcategory(Request $request)
    {
        // Validasi input
        $request->validate([
            'sub_category_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Cek apakah subcategory dengan nama yang sama sudah ada
        $existingSubcategory = BudgetSubcategory::where('category_id', $request->category_id)
            ->where('sub_category_name', $request->sub_category_name)
            ->first();

        if ($existingSubcategory) {
            return redirect()->back()->withErrors(['error' => 'Subcategory already exists for this category.']);
        }

        // Tambah subcategory baru
        BudgetSubcategory::create([
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category_name,
        ]);

        return redirect()->back()->with('success', 'Subcategory added successfully.');
    }


    public function storeDescription(Request $request)
    {
        // Validasi input
        $request->validate([
            'description_text' => 'required|string',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        // Cek apakah description dengan teks yang sama sudah ada
        $existingDescription = BudgetDescription::where('sub_category_id', $request->sub_category_id)
            ->where('description_text', $request->description_text)
            ->first();

        if ($existingDescription) {
            return redirect()->back()->withErrors(['error' => 'Description already exists for this subcategory.']);
        }

        // Tambah description baru
        BudgetDescription::create([
            'sub_category_id' => $request->sub_category_id,
            'description_text' => $request->description_text,
        ]);

        return redirect()->back()->with('success', 'Description added successfully.');
    }

    function planned_budget($costReview)
    {
        $costReview = CostReview::find($costReview);

        $categories = BudgetCategory::where('cost_review_id', $costReview->id)
            ->with(['subcategory.descriptions'])
            ->get();

        return view('control_budget.cost_review.budget_planed', compact('categories', 'costReview'));
    }

    function plan_budget(Request $request)
    {
        $request->validate([
            'months' => 'required|array',
            'planned_budgets' => 'required|array',
            'year' => 'required'
        ]);

        $months = $request->months;
        $plannedBudgets = $request->planned_budgets;
        $year = $request->year;

        foreach ($months as $month) {
            foreach ($plannedBudgets as $descriptionId => $plannedBudget) {
                // Hapus titik (.) dari input yang diformat ke dalam angka asli
                $plannedBudget = str_replace('.', '', $plannedBudget);

                // Pastikan tidak ada nilai kosong, jika kosong set nilai default (misalnya 0)
                if (empty($plannedBudget)) {
                    $plannedBudget = 0;
                } else {
                    $plannedBudget = floatval($plannedBudget); // Mengubah menjadi angka
                }

                MonthlyBudget::create([
                    'description_id' => $descriptionId,
                    'planned_budget' => $plannedBudget,
                    'month' => $month,
                    'year' => $year
                ]);
            }
        }

        return redirect('/control-budget')->with('success', 'Budget has been successfully planned.');
    }

    public function review_cost(Request $request, $costReviewId)
    {
        // Dapatkan cost review terkait
        $costReview = CostReview::find($costReviewId);

        // Ambil tahun yang dipilih dari query parameter atau default ke tahun sekarang
        $selectedYear = $request->query('year', date('Y'));

        // Ambil bulan yang dipilih dari query parameter atau default ke bulan sekarang
        $selectedMonth = $request->query('month', date('F'));

        // Ambil data kategori dengan subkategori dan deskripsi yang terkait
        $categories = BudgetCategory::with(['subcategory.descriptions.monthly_budget' => function ($query) use ($selectedYear, $selectedMonth, $costReviewId) {
            $query->where('year', $selectedYear)
                ->where('month', $selectedMonth);
        }])->where('cost_review_id', $costReviewId)->get();

        // dd($categories);

        // Ambil daftar tahun untuk dropdown
        $years = MonthlyBudget::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Ambil daftar bulan untuk pagination
        $months = MonthlyBudget::select('month')
            ->where('year', $selectedYear)
            ->distinct()
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
            ->pluck('month');

        return view('control_budget.cost_review.review_cost', compact('costReview', 'categories', 'years', 'months', 'selectedYear', 'selectedMonth'));
    }
}
