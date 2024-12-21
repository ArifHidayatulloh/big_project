<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sipil extends Model
{
    use HasFactory;
    protected $table = 'sipils';
    protected $guarded = ['id'];

    public function progress(){
        return $this->hasMany(SipilProgress::class);
    }
}
