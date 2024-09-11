<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategorie extends Model
{
    use HasFactory;

    protected $table = 'budget_categories';
    protected $guarded = ['id'];

    public function subcategories(){
        return $this->hasMany(BudgetSubCategorie::class, 'category_id');
    }

}
