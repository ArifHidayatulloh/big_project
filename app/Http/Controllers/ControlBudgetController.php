<?php

namespace App\Http\Controllers;

use App\Models\BudgetCategorie;
use App\Models\BudgetDescription;
use App\Models\BudgetSubCategorie;
use App\Models\DepartmenUser;
use App\Models\MonthlyBudget;
use App\Models\Unit;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ControlBudgetController extends Controller
{
    function index()
    {
        if (Auth::user()->role == 1) {
            $departmentIds = DepartmenUser::where('user_id', Auth::user()->id)->pluck('unit_id');
            if ($departmentIds->isNotEmpty()) {
                $departments = Unit::where('id', $departmentIds)->get();
            } else {
                $departments = Unit::all();
            }
        } elseif(Auth::user()->role == 2){
            $departments = Unit::all();
        }elseif (Auth::user()->role == 3) {
            $departmentIds = DepartmenUser::where('user_id', Auth::user()->id)->pluck('unit_id');
            $departments = Unit::where('id', $departmentIds)->get();
        }elseif(Auth::user()->role == 4){
            if(Auth::user()->unit_id != null){
                $departmentIds = DepartmenUser::where('user_id', Auth::user()->id)->pluck('unit_id');
                $departments = Unit::where('id', $departmentIds)->get();
            }else{
                $departments = Unit::where('id', Auth::user()->unit_id)->get();
            }
        }else{
            $departments = Unit::where('id', Auth::user()->unit_id)->get();
        }

        return view('control_budget.index', [
            'departments' => $departments,
        ]);
    }

    function show(Unit $unit)
    {
        // Ambil semua budget categories beserta subcategories dan descriptions
        $budgetCategory = BudgetCategorie::where('unit_id', $unit->id)->with('subcategories.descriptions')->get();

        return view('control_budget.descriptions.index', [
            'budgetCategories' => $budgetCategory,
            'unit' => $unit,
        ]);
    }

    function storeCategory(Request $request)
    {
        $data = $request->validate([
            'unit_id' => ['required'],
            'name' => ['required'],
        ]);

        $cek = BudgetCategorie::firstOrNew($data);
        if ($cek->exists) {
            return back()->withErrors(['data' => 'A category already exists.'])->withInput();
        } else {
            BudgetCategorie::create($data);
            return back()->with('success', 'Category has been successfully added.');
        }
    }

    function updateCategory(Request $request, BudgetCategorie $category)
    {
        $data = $request->validate([
            'unit_id' => ['required'], // Tambahkan validasi unit_id jika memang diperlukan
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cek = BudgetCategorie::where('name', $data['name'])
            ->where('unit_id', $data['unit_id'])
            ->where('id', '!=', $category->id)->exists();

        if ($cek) {
            return back()->withErrors(['data' => 'A category with this name already exists.'])->withInput();
        } else {
            $category->update($data);
            return back()->with('success', 'Category has been updated successfully.');
        }
    }

    function destroyCategory(BudgetCategorie $category)
    {
        if ($category->subcategories()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete category with subcategories.']);
        }

        $category->delete();
        return back()->with('success', 'Category has been deleted successfully.');
    }

    function storeSubcategory(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required'],
            'name' => ['required'],
        ]);

        $cek = BudgetSubCategorie::firstOrNew($data);
        if ($cek->exists) {
            return back()->withErrors(['data' => 'A subcategory already exists.'])->withInput();
        } else {
            BudgetSubCategorie::create($data);
            return back()->with('success', 'Subcategory has been successfully added.');
        }
    }

    function updateSubcategory(Request $request, BudgetSubCategorie $subcategory)
    {
        $data = $request->validate([
            'category_id' => ['required'],
            'name' => ['required'],
        ]);

        $cek = BudgetSubCategorie::where('name', $data['name'])
            ->where('category_id', $data['category_id'])
            ->where('id', '!=', $subcategory->id)->exists();

        if ($cek) {
            return back()->withErrors(['data' => 'A subcategory with this name already exists.'])->withInput();
        }

        $subcategory->update($data);
        return back()->with('success', 'Subcategory has been updated successfully.');
    }

    function destroySubcategory(BudgetSubCategorie $subcategory)
    {
        if ($subcategory->descriptions()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete subcategory with descriptions.']);
        }

        $subcategory->delete();
        return back()->with('success', 'Subcategory has been deleted successfully.');
    }

    function storeDescription(Request $request)
    {
        $data = $request->validate([
            'subcategory_id' => ['required'],
            'description' => ['required'],
        ]);

        $cek = BudgetDescription::firstOrNew($data);
        if ($cek->exists) {
            return back()->withErrors(['data' => 'A description already exists.'])->withInput();
        } else {
            BudgetDescription::create($data);
            return back()->with('success', 'Description has been successfully added.');
        }
    }

    function updateDescription(Request $request, BudgetDescription $description)
    {
        $data = $request->validate([
            'subcategory_id' => ['required'],
            'description' => ['required', 'string', 'max:255'], // Tambahkan validasi tipe dan batas panjang
        ]);

        // Cek apakah sudah ada deskripsi yang sama untuk subkategori yang sama, kecuali yang sedang di-update
        $cek = BudgetDescription::where('subcategory_id', $data['subcategory_id'])
            ->where('description', $data['description'])
            ->where('id', '!=', $description->id)
            ->exists();

        if ($cek) {
            return back()->withErrors(['data' => 'A description with this subcategory and description already exists.'])->withInput();
        }

        // Lakukan update dengan data baru
        $description->update($data);
        return back()->with('success', 'Description has been updated successfully.');
    }


    function destroyDescription(BudgetDescription $description)
    {
        $description_exist = MonthlyBudget::where('description_id', $description->id)->exists();
        if ($description_exist) {
            return back()->withErrors(['description_id' => 'This description has budget assigned.'])->withInput();
        }

        $description->delete();
        return back()->with('success', 'Description has been deleted successfully.');
    }

    // Monthly Budget
    function formMonthlyBudget(Unit $unit)
    {
        $categories = BudgetCategorie::where('unit_id', $unit->id)->with('subcategories.descriptions')->get();

        return view('control_budget.budgets.monthly-budget', compact('categories', 'unit'));
    }

    public function storeMonthlyBudget(Request $request)
    {
        $budgets = $request->input('budget'); // Ambil budget amount dari input form
        $year = $request->input('year'); // Ambil tahun dari input form
        $month = $request->input('month'); // Ambil bulan dari input form

        foreach ($budgets as $descriptionId => $amount) {
            // Hapus pemisah selain angka dan desimal
            $cleanedAmount = preg_replace('/[^\d,]/', '', $amount); // Hapus semua karakter kecuali angka dan koma
            $cleanedAmount = str_replace(',', '.', $cleanedAmount); // Ganti koma dengan titik untuk desimal

            $existingBudget = MonthlyBudget::where('description_id', $descriptionId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existingBudget) {
                $existingBudget->update([
                    'budget_amount' => $cleanedAmount, // Simpan amount yang telah dibersihkan
                ]);
            } else {
                MonthlyBudget::create([
                    'description_id' => $descriptionId,
                    'budget_amount' => $cleanedAmount, // Simpan amount yang telah dibersihkan
                    'year' => $year,
                    'month' => $month,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Monthly budget has been successfully saved.');
    }

    public function costOverview(Unit $unit)
    {
        // Set default year and month
        $year = null;
        $month = null;

        return view('control_budget.budgets.cost-review', compact('unit', 'year', 'month'));
    }

    public function costReview(Request $request, Unit $unit)
    {
        // Validasi input
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        // Query data berdasarkan unit, tahun, bulan yang dipilih
        $budgets = MonthlyBudget::whereHas('description.subcategory.category', function ($query) use ($unit) {
            $query->where('unit_id', $unit->id);
        })
            ->where('year', $year)
            ->where('month', $month)
            ->with(['description.subcategory.category', 'expenses'])
            ->get();

        // Kelompokkan data berdasarkan kategori
        $groupedBudgets = $budgets->groupBy(function ($budget) {
            return $budget->description->subcategory->category->id;
        })->map(function ($categoryBudgets) {
            return $categoryBudgets->groupBy(function ($budget) {
                return $budget->description->subcategory->id;
            });
        });

        // Hitung selisih (Var) antara budget dan actual expense
        foreach ($budgets as $budget) {
            $budget->variance = $budget->budget_amount - $budget->expenses->sum('amount');
        }

        // Hitung total per kategori
        $categoryTotals = $groupedBudgets->map(function ($subcategories) {
            return [
                'totalPlan' => $subcategories->flatten(1)->sum('budget_amount'),
                'totalAct' => $subcategories->flatten(1)->sum(function ($budget) {
                    return $budget->expenses->sum('amount');
                }),
                'totalVar' => $subcategories->flatten(1)->sum(function ($budget) {
                    return $budget->variance;
                }),
            ];
        });

        // Hitung total keseluruhan
        $overallTotals = [
            'totalPlan' => $budgets->sum('budget_amount'),
            'totalAct' => $budgets->flatMap(function ($budget) {
                return $budget->expenses;
            })->sum('amount'),
            'totalVar' => $budgets->sum(function ($budget) {
                return $budget->variance;
            }),
        ];

        return view('control_budget.budgets.cost-review', compact('groupedBudgets', 'unit', 'year', 'month', 'categoryTotals', 'overallTotals'));
    }

    public function editMonthlyBudget(Request $request, Unit $unit)
    {
        $year = $request->input('year');
        $month = $request->input('month');


        // Ambil semua kategori beserta subkategori dan deskripsi
        $categories = BudgetCategorie::where('unit_id', $unit->id)->with('subcategories.descriptions')->get();

        // Ambil budget bulanan yang sudah ada untuk year dan month yang dipilih
        $budgets = MonthlyBudget::where('year', $year)
            ->where('month', $month)
            ->whereHas('description.subcategory.category', function ($query) use ($unit) {
                $query->where('unit_id', $unit->id);
            })
            ->get()
            ->keyBy('description_id'); // Buat array dengan description_id sebagai key

        return view('control_budget.budgets.edit-monthly-budget', compact('categories', 'unit', 'budgets', 'year', 'month'));
    }

    public function updateMonthlyBudget(Request $request, Unit $unit)
    {
        // Validasi input
        $request->validate([
            'year' => 'required|integer|min:2020|max:' . date('Y'),
            'month' => 'required|integer|min:1|max:12',
            'budget.*' => 'required|regex:/^Rp \d{1,3}(\.\d{3})*(,\d+)?$/',
        ]);

        $budgets = $request->input('budget');
        $year = $request->input('year');
        $month = $request->input('month');

        foreach ($budgets as $descriptionId => $amount) {
            // Hapus format Rupiah dan ubah pemisah desimal
            $cleanedAmount = preg_replace('/[^\d,]/', '', $amount); // Menghapus karakter selain angka dan koma
            $cleanedAmount = str_replace(',', '.', $cleanedAmount); // Mengganti koma dengan titik untuk format desimal

            // Cek apakah budget sudah ada
            $existingBudget = MonthlyBudget::where('description_id', $descriptionId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existingBudget) {
                // Update budget yang sudah ada
                $existingBudget->update([
                    'budget_amount' => $cleanedAmount,
                ]);
            } else {
                // Buat budget baru
                MonthlyBudget::create([
                    'description_id' => $descriptionId,
                    'budget_amount' => $cleanedAmount,
                    'year' => $year,
                    'month' => $month,
                ]);
            }
        }

        // Redirect ke halaman overview dengan pesan sukses
        return redirect("/control-budget/cost-review/{$unit->id}?year={$year}&month={$month}")->with(
            'success',
            'Monthly budget has been updated successfully.',
        );
    }



    // expenses
    public function expenseShow(Unit $unit)
    {
        $year = null;
        $month = null;
        $groupedBudgets = null;

        return view('control_budget.budgets.input-expenses', compact('year', 'month', 'unit', 'groupedBudgets'));
    }

    public function inputExpensesForm(Request $request, Unit $unit)
    {
        // Validasi input tahun dan bulan
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        // Query budgets berdasarkan unit, tahun, dan bulan
        $budgets = MonthlyBudget::whereHas('description.subcategory.category', function ($query) use ($unit) {
            $query->where('unit_id', $unit->id);
        })
            ->where('year', $year)
            ->where('month', $month)
            ->with(['description.subcategory.category'])
            ->get();

        // Query expenses berdasarkan unit, tahun, dan bulan
        $expenses = Expense::whereHas('monthlyBudget', function ($query) use ($unit) {
            $query->whereHas('description.subcategory.category', function ($query) use ($unit) {
                $query->where('unit_id', $unit->id);
            });
        })->get();


        // Mengelompokkan budget berdasarkan kategori dan subkategori
        $groupedBudgets = $budgets->groupBy(function ($budget) {
            return $budget->description->subcategory->category->id;
        })->map(function ($subcategories) {
            return $subcategories->groupBy(function ($budget) {
                return $budget->description->subcategory->id;
            });
        });

        return view('control_budget.budgets.input-expenses', compact('groupedBudgets', 'unit', 'year', 'month', 'expenses'));
    }



    public function storeExpenses(Request $request, Unit $unit)
    {
        // Validasi input
        $validated = $request->validate([
            'expenses' => 'nullable|array',
            'expenses.*.amount' => 'nullable|numeric|min:0',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        // Loop melalui setiap budget dan simpan atau update pengeluarannya
        foreach ($validated['expenses'] as $budgetId => $expenseData) {
            // Pastikan budget exist
            $budget = MonthlyBudget::find($budgetId);

            if ($budget) {
                // Cek apakah expense dengan budget_id, year, dan month sudah ada
                Expense::updateOrCreate(
                    [
                        'budget_id' => $budgetId, // Kondisi yang dicek (budget_id yang sesuai)
                    ],
                    [
                        'amount' => $expenseData['amount'], // Jika ada, update amount
                        'date' => now(),                   // Update atau buat dengan tanggal sekarang
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Expenses have been saved successfully.');
    }
}
