<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentNotification extends Model
{
    protected $fillable = [
        'agent_id',        // user_id from users table
        'title',
        'message',
        'type',            // assignment, status, system, message
        'data',            // JSON: {shipment_id: 123, ...}
        'is_read',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime'
    ];

    // Relationship with agent (User)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Get notification for specific agent
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    // Unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
