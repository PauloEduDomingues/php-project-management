<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'colaborator_id',
        'project_id'
    ];

    public function project () {
        return $this->belongsTo(Project::class);
    }
}
