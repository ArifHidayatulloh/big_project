<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDescriptionGrouping extends Model
{
    use HasFactory;
    protected $table = 'description_grouping';
    protected $guarded = ['id'];

    public function subcategory(){
        return $this->belongsTo(BudgetSubcategory::class, 'sub_category_id');
    }
    public function descriptions(){
        return $this->hasMany(BudgetDescription::class, 'description_grouping_id');
    }
}
