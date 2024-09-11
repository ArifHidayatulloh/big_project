<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table = 'expenses';
    protected $guarded = ['id'];

    // Model Expense
    public function monthlyBudget()
    {
        return $this->belongsTo(MonthlyBudget::class, 'budget_id');
    }
}
