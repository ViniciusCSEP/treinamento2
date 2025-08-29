<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'especiality',
        'start_hour',
        'end_hour',
        'break',
        'user_id',
        'week_days'
    ];

    protected $casts = [
        'week_days' => 'array',
        'start_hour' => 'datetime:H:i',
        'end_hour' => 'datetime:H:i'
    ];

    public $timestamps = false;

    protected $appends = ['break_minutes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBreakMinutesAttribute()
    {
        $break = $this->break;

        list($horas, $minutos, $segundos) = explode(':', $break);

        $intervalo = ($horas * 60) + $minutos;

        return (int) $intervalo;
    }
}

