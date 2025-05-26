<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'admin_notes',
        'admin_response',
        'response_sent',
        'conversation_id' // Add conversation ID to group related messages
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'response_sent' => 'boolean',
    ];

    /**
     * Mendapatkan admin yang merespons pesan
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Mengecek apakah pesan sudah direspons oleh admin
     */
    public function isResponded()
    {
        return !is_null($this->responded_at);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for filtering user's messages
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope for getting conversation threads
    public function scopeConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId)
                    ->orderBy('created_at', 'asc');
    }
}