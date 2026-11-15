<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DnsNode extends Model { protected $table='dns_nodes'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean','last_checked_at'=>'datetime']; }
