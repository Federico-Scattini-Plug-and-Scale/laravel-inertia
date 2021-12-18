<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
    	'is_agency' => 'boolean'
    ];

    public function user() 
    { 
      	return $this->morphOne('App\Models\User', 'detail');
    }
}
