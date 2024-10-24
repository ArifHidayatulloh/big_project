<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatePic extends Model
{
    use HasFactory;

    protected $table = 'update_pics';
    protected $guarded = ['id'];

    public function commentDephead(){
        return $this->belongsTo(CommentDephead::class);
    }

    public function updator(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
