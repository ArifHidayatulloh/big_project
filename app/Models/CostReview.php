<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostReview extends Model
{
    use HasFactory;

    protected $table = 'cost_reviews';
    protected $guarded = ['id'];

    public function unit(){
        return $this->belongsTo(Unit::class,'unit_id','id');
    }

    public function category(){
        return $this->hasMany(BudgetCategory::class);
    }

    public function budget(){
        return $this->belongsTo(MonthlyBudget::class);
    }
}
