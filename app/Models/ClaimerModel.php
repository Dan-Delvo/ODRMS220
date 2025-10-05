<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimerModel extends Model
{
    use HasFactory;

    // Specify the table name if it's not pluralized automatically
    protected $connection = 'mysql_local';
    protected $table = 'clm_claimers';
    protected $primaryKey = 'id';
    public $timestamps = false;
    // Define the fillable fields
    protected $fillable = [
        'Fname',
        'Lname',
        'contact_no',
        'claimed_date',
        'synced',           // Add these 4 lines
        'needs_sync',
        'synced_at',
        'online_id',
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'clm_claimers_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->Fname} {$this->Lname}";
    }

}
