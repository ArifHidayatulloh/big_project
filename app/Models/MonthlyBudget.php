<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyBudget extends Model
{
    use HasFactory;

    protected $table = 'monthly_budgets';
    protected $guarded = ['id'];

    // Model MonthlyBudget
    public function description()
    {
        return $this->belongsTo(BudgetDescription::class, 'description_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'budget_id');
    }
}
