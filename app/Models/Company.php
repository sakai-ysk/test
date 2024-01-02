<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function products()
{
    return $this->hasMany(Product::class);
}

//1/2追記
public function getLists()
{
    $companies = Company::pluck('company_name', 'id');
    return $companies;
}
//
}
