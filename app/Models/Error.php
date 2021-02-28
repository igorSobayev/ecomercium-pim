<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory;

    protected $table = 'pim_errors';
    protected $primaryKey = 'id_error';

    protected $fillable = [
        'error'
    ];
}
