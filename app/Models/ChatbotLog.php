<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_message',
        'detected_intent',
        'bot_response',
        'response_time_ms',
        'was_helpful'
    ];

    protected $casts = [
        'was_helpful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
