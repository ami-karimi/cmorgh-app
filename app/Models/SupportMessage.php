<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportMessage extends Model { protected $table='support_messages'; protected $guarded=['id']; protected $casts=['is_internal'=>'boolean']; public function ticket(){return $this->belongsTo(SupportTicket::class,'ticket_id');} public function user(){return $this->belongsTo(User::class);} public function attachments(){return $this->hasMany(SupportAttachment::class,'message_id');} }
