<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalAgreement extends Model
{
    use HasFactory;
    const IS_OWNER_TYPE = 2;
    const IS_USER_TYPE = 1;
    protected $guarded= ['id'];
    protected $table = 'rental_agreement';

}
