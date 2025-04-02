<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesModel extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'role';

    // Primary key of the table (default is 'id')
    protected $primaryKey = 'id';
    public $incrementing = true;


    // Indicates if the model should use timestamps (created_at and updated_at columns)
    public $timestamps = false;

    // Specify the fillable attributes for mass assignment
    protected $fillable = [
        'id',
        'name',
    ];

    // Specify any hidden attributes that should not be returned in queries
    protected $hidden = [];


    // Example relationships (adjust as needed):
    // Assuming roles are related to users
    public function accounts()
    {
        return $this->hasMany(Account::class, 'role_id', 'id');
    }

    static public function getSingle($id)
    {
        return RolesModel::find($id);
    }

    static public function getRecord()
    {
        return RolesModel::get();
    }
}

