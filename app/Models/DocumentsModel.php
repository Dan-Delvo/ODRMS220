<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsModel extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'doc_categories';

    // Define the primary key column
    protected $primaryKey = 'id';

    // Disable timestamps if the table doesn't have `created_at` and `updated_at`
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'DocType',
    ];

    // Optionally, you can define custom methods or accessors here
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'doc_categories_id', 'id');
    }

}
