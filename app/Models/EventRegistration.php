<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'code', 'status', 'amount', 'registered_at'];

    protected $casts = [
        'registered_at' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model - auto-generate code if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate code if not provided
            if (empty($model->code)) {
                $code = self::generateUniqueCode();
                $model->code = $code;
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function fieldAnswers()
    {
        return $this->hasMany(EventFieldAnswer::class);
    }

    /**
     * Generate a unique registration code - doesn't require event to be loaded
     */
    public static function generateUniqueCode(): string
    {
        do {
            $timestamp = now()->timestamp;
            $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $code = "REG-{$timestamp}-{$random}";
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Generate a registration code with event prefix
     * @deprecated Use generateUniqueCode() instead
     */
    public static function generateCode(Event $event): string
    {
        $eventPrefix = substr(strtoupper($event->title ?? 'EVT'), 0, 3);
        $timestamp = now()->timestamp;
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        return "{$eventPrefix}-{$timestamp}-{$random}";
    }
}
