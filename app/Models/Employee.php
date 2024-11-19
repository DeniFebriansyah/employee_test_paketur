<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use SoftDeletes, HasFactory;
    protected $guarded = ['id'];
    public function company(){
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
