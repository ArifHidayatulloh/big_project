<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDescription extends Model
{
    use HasFactory;
    protected $table = 'descriptions';
    protected $guarded = ['id'];

    public function subcategory(){
        return $this->belongsTo(BudgetSubcategory::class, 'sub_category_id','id');
    }

    public function monthly_budget(){
        return $this->hasMany(MonthlyBudget::class,'description_id');
    }
}
