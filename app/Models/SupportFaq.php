<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportFaq extends Model { protected $table='support_faqs'; protected $guarded=['id']; protected $casts=['keywords'=>'array','is_published'=>'boolean']; public function category(){return $this->belongsTo(SupportCategory::class,'category_id');} public function scopePublished($q){return $q->where('is_published',true);} }
