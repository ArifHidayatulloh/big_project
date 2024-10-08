<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDescription extends Model
{
    use HasFactory;

    protected $table = 'budget_descriptions';
    protected $guarded = ['id'];

    // Model BudgetDescription
    public function subcategory()
    {
        return $this->belongsTo(BudgetSubcategorie::class, 'subcategory_id');
    }
}
