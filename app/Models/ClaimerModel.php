<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimerModel extends Model
{
    use HasFactory;

    // Specify the table name if it's not pluralized automatically
    protected $table = 'clm_claimers';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    // Define the fillable fields
    protected $fillable = [
        'Fname',
        'Lname',
        'contact_no',
        'claimed_date'
    ];

    // Relationship with individual document requests
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'clm_claimers_id', 'id');
    }

    // ✅ Relationship with bulk requests
    public function bulkRequests()
    {
        return $this->hasMany(BulkRequest::class, 'claimer_id', 'id');
    }

    // Full name accessor
    public function getFullNameAttribute()
    {
        return "{$this->Fname} {$this->Lname}";
    }
}
