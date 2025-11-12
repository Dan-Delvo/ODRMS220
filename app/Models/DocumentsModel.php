<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsModel extends Model
{
    use HasFactory;

    protected $table = 'doc_categories';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'DocType',
        'DocPrice'
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'doc_categories_id', 'id');
    }

    // ✅ FIXED: Helper method to get request statistics
    public function getRequestStatistics()
    {
        return [
            'total' => $this->documentRequests()->count(),
            'pending' => $this->documentRequests()->where('status', 'Pending')->count(),
            'processing' => $this->documentRequests()->where('status', 'Processing')->count(),
            'for_release' => $this->documentRequests()->where('status', 'For Release')->count(),
            'claimed' => $this->documentRequests()->where('status', 'Claimed')->count(),
            'declined' => $this->documentRequests()->where('status', 'Declined')->count(),
            'active' => $this->documentRequests()->whereIn('status', ['Pending', 'Processing', 'For Release'])->count(),
        ];
    }

    // ✅ Helper method to check if document can be deleted
    public function canBeDeleted()
    {
        return $this->documentRequests()
            ->whereIn('status', ['Pending', 'Processing', 'For Release'])
            ->count() === 0;
    }

    // ✅ Get deletion blocking message
    public function getDeletionBlockMessage()
    {
        $stats = $this->getRequestStatistics();
        
        if ($stats['active'] > 0) {
            $breakdown = [];
            if ($stats['pending'] > 0) $breakdown[] = "{$stats['pending']} Pending";
            if ($stats['processing'] > 0) $breakdown[] = "{$stats['processing']} Processing";
            if ($stats['for_release'] > 0) $breakdown[] = "{$stats['for_release']} For Release";
            
            $breakdownText = implode(', ', $breakdown);
            return "This document has {$stats['active']} active request(s) ({$breakdownText})";
        }

        if ($stats['total'] > 0) {
            $history = [];
            if ($stats['claimed'] > 0) $history[] = "{$stats['claimed']} completed";
            if ($stats['declined'] > 0) $history[] = "{$stats['declined']} declined";
            
            $historyText = implode(' and ', $history);
            return "This document has {$stats['total']} historical request(s) ({$historyText})";
        }

        return null;
    }
}
