<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetSubCategorie extends Model
{
    use HasFactory;

    protected $table = 'budget_subcategories';
    protected $guarded = ['id'];

    public function category(){
        return $this->belongsTo(BudgetCategorie::class, 'category_id');
    }

    public function descriptions(){
        return $this->hasMany(BudgetDescription::class, 'subcategory_id');
    }

}
