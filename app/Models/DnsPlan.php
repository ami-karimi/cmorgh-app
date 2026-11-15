<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DnsPlan extends Model { protected $table='dns_plans'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean']; public function subscriptions(){return $this->hasMany(DnsSubscription::class,'plan_id');} }
