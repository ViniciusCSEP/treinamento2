<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
Carbon::setlocale('pt-br');

class Consult extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'reason',
        'hour',
        'date',
        'patient_id',
        'doctor_id'
    ];

    public $timestamps = false;

    protected $casts = [
        'hour' => 'datetime:H:i',
    ];

    public function getDateAttribute($value) {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }

    public function doctor(): BelongsTo {
        return $this->belongsTo(Doctor::class);
    }
}
