<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $guarded = ['id'];

    public function cost_review(){
        return $this->belongsTo(CostReview::class, 'cost_review_id','id');
    }

    public function subcategory(){
        return $this->hasMany(BudgetSubcategory::class,'category_id');
    }
}
