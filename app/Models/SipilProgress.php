<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipilProgress extends Model
{
    use HasFactory;

    protected $table = 'sipil_progress';
    protected $guarded = ['id'];

    public function sipil(){
        return $this->belongsTo(Sipil::class, 'sipil_id', 'id');
    }

    public function approval(){
        return $this->hasMany(SipilProgressApproval::class);
    }
}
