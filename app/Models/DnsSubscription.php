<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DnsSubscription extends Model { protected $table='dns_subscriptions'; protected $guarded=['id']; protected $casts=['starts_at'=>'datetime','expires_at'=>'datetime','metadata'=>'array']; public function user(){return $this->belongsTo(User::class);} public function plan(){return $this->belongsTo(DnsPlan::class,'plan_id');} public function ips(){return $this->hasMany(DnsSubscriptionIp::class,'subscription_id');} public function scopeActive($q){return $q->where('status','active')->where(function($x){$x->whereNull('expires_at')->orWhere('expires_at','>',now());});} }
