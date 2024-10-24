<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingList extends Model
{
    use HasFactory;
    protected $table = 'working_lists';
    protected $guarded = ['id'];

    protected $casts = [
        'relatedpic' => 'array', // Ini akan mengonversi JSON ke array
    ];

    // Accessor untuk relatedpic
    public function getRelatedpicAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
    // Accessor to get related PIC names
    public function getRelatedPicNamesAttribute()
    {
        $relatedPicNiks = $this->relatedpic;
        $relatedPicNames = [];

        if ($relatedPicNiks) {
            $relatedPicNames = User::whereIn('id', $relatedPicNiks)->pluck('name', 'id')->toArray();
        }

        return $relatedPicNames;
    }
    public function department()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function commentDepheads()
    {
        return $this->hasMany(CommentDephead::class);
    }
}
