<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    
    const UPDATED_AT = null;

    protected $table = 'ModelPasswordReset';
  
    protected $fillable = [
        'email',
        'token'
    ];
}
