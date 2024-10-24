<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentDephead extends Model
{
    use HasFactory;

    protected $table = 'comment_depheads';
    protected $guarded = ['id'];

    public function workingList(){
        return $this->belongsTo(WorkingList::class);
    }

    public function updatePics(){
        return $this->hasMany(UpdatePic::class);
    }
}
