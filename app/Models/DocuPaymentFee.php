<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocuPaymentFee extends Model
{
    // Specify the table name if it's not the plural of the model name
    protected $table = 'docu_payment_fees';

    // If your table doesn't have 'id' as a primary key
    protected $primaryKey = 'receipt_no';

    // If the primary key is not auto-incrementing
    public $incrementing = false;

    // If the primary key is not of type integer
    protected $keyType = 'int';

    // If your table doesn't use Laravel's created_at and updated_at columns
    public $timestamps = false;

    // Mass assignable attributes
    protected $fillable = [
        'receipt_no',
        'docu_categories_id',
        'doc_amount',
        'name_request',
        'time_request',
    ];

    public function document()
    {
        return $this->belongsTo(DocumentsModel::class, 'docu_categories_id', 'id');
    }
}
