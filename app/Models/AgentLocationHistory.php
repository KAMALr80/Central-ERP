<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLocationHistory extends Model
{
    protected $table = 'agent_location_histories';

    protected $fillable = [
        'agent_id',
        'shipment_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'bearing',
        'battery_level',
        'recorded_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'recorded_at' => 'datetime'
    ];

    // Relationship with agent (User)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Relationship with shipment
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
