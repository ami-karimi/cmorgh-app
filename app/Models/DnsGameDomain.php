<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DnsGameDomain extends Model { protected $table='dns_game_domains'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean','last_checked_at'=>'datetime']; }
