<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
class Periode extends Model
{
 
    protected $primaryKey = 'id';//default: id
    protected $keyType = 'integer';//default: biginteger
    protected $table = 'periode';
    protected $fillable = [
        'id',
        'aktif',
        
    ];

   
    
}
