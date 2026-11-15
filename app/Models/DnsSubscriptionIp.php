<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DnsSubscriptionIp extends Model { protected $table='dns_subscription_ips'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean','last_seen_at'=>'datetime']; public function subscription(){return $this->belongsTo(DnsSubscription::class,'subscription_id');} }
