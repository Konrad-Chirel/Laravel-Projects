<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    protected $fillable = ['name', 'priority_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function priority() {
        return $this->belongsTo(Priority::class);
    }
}
