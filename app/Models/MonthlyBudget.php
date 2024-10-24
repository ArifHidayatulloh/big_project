<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyBudget extends Model
{
    use HasFactory;
    protected $table ='monthly_budgets';
    protected $guarded = ['id'];

    public function description(){
        return $this->belongsTo(BudgetDescription::class, 'description_id','id');
    }

    public function actual(){
        return $this->hasMany(Actual::class, 'monthly_budget_id');
    }
}
