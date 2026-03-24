<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class BreachTimeline extends Model
{
    use HasFactory;

    protected $fillable = ['breach_incident_id', 'user_id', 'action', 'description', 'attachment_path'];

    public function breach() { return $this->belongsTo(BreachIncident::class, 'breach_incident_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
