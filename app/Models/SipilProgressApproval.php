<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipilProgressApproval extends Model
{
    use HasFactory;

    protected $table = 'sipil_progress_approvals';
    protected $guarded = ['id'];

    public function progress(){
        return $this->belongsTo(SipilProgress::class);
    } 
}
