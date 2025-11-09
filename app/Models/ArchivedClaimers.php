<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedClaimers extends Model
{
    //

    protected $table = 'clm_claimers_archived';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Define the fillable fields
    protected $fillable = [
        'Fname',
        'Lname',
        'contact_no',
        'claimed_date'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->Fname} {$this->Lname}";
    }

}
