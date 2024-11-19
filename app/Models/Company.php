<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use SoftDeletes, HasFactory;
    protected $guarded = ['id'];
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
