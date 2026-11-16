<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DownloadCategory extends Model { protected $table='download_categories'; protected $guarded=['id']; protected $casts=['is_active'=>'boolean']; public function items(){return $this->hasMany(DownloadItem::class,'category_id');} }
