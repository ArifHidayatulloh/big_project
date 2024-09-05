<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DepartmenUser extends Model
{
    use HasFactory;
    protected $table = 'departmens_users';
    protected $guarded = ['id'];

    public function department(){
        return $this->belongsTo(Unit::class,'unit_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
