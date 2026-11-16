<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportCategory extends Model { protected $table='support_categories'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean']; public function faqs(){return $this->hasMany(SupportFaq::class,'category_id');} public function tickets(){return $this->hasMany(SupportTicket::class,'category_id');} }
