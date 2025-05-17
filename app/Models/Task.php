<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    protected $table = 'tasks';

    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'status'
    ];

    public function user()
    {
        return $this->belongTo(User::class, 'user_id');
    }
}
