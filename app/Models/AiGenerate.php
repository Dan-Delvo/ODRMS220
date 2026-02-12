<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

class AiGenerate extends Model
{
    public $timestamps = false;

    protected $table = 'ai_generate';

    protected $fillable = [
        'ai_output',
        'date_generated',
    ];

    protected $casts = [
        'ai_output' => 'array',
        'date_generated' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->date_generated = $model->date_generated ?? Carbon::now();
        });
    }

    public function getOutput()
    {
        return self::whereDate('date_generated', Carbon::today())->latest('date_generated')->first();
    }

    public static function insertOutput($output) {
        try {
            self::create([
                'ai_output' => $output
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
