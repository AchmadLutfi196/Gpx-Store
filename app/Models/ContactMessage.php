<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'admin_notes',
        'admin_response',
        'responded_at',
        'responded_by',
        'response_sent',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'responded_at' => 'datetime',
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
}