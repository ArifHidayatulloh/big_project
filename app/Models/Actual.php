<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actual extends Model
{
    use HasFactory;
    protected $table = 'actuals';
    protected $guarded = ['id'];

    public function monthly_budget(){
        return $this->belongsTo(MonthlyBudget::class,'monthly_budget_id','id');
    }
}
