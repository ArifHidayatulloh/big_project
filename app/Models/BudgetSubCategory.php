<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetSubCategory extends Model
{
    use HasFactory;
    protected $table = 'sub_categories';
    protected $guarded = ['id'];

    public function category(){
        return $this->belongsTo(BudgetCategory::class, 'category_id','id');
    }

    public function description_grouping(){
        return $this->hasMany(BudgetDescriptionGrouping::class,'sub_category_id');
    }

    public function descriptions(){
        return $this->hasMany(BudgetDescription::class,'sub_category_id');
    }
}
