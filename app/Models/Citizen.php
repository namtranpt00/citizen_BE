<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;
    protected $table = 'citizen';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'ID_number',
        'date_of_birth',
        'gender',
        'hometown',
        'permanent_address',
        'temporary_address',
        'religion',
        'education_level',
        'job',
    ];
}
