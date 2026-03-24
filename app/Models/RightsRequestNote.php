<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class RightsRequestNote extends Model
{
    use HasFactory;

    protected $fillable = ['rights_request_id', 'user_id', 'note', 'is_internal', 'attachment_path'];

    public function rightsRequest() { return $this->belongsTo(RightsRequest::class); }
    public function user() { return $this->belongsTo(User::class); }
}
