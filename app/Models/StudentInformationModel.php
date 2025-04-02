<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInformationModel extends Model
{
    use HasFactory;
    protected $table = 'std_students'; // Replace with your actual table name
    protected $primaryKey = 'id'; // Primary key column
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Specify the primary key type as string
    public $timestamps = false;

    protected $fillable = [
        'id',
        'FirstName',
        'LastName',
        'MiddleName',
        'Suffix',
        'LRN',
        'Grade_level',
        'Std_status',
        'Last_sy_attended'
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate a unique integer ID for the primary key
        static::creating(function ($model) {
            // Generate a unique integer ID by using the current timestamp (in seconds)
            // or another strategy that suits your needs
            $model->id = (int) now()->timestamp . mt_rand(1000, 9999);
        });
    }


    // Optionally, you can define custom methods or accessors here
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'std_students_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->FirstName} {$this->LastName}";
    }

}
