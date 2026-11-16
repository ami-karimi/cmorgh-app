<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class DownloadItem extends Model
{
    protected $table='download_items'; protected $guarded=['id']; protected $casts=['is_featured'=>'boolean','is_published'=>'boolean'];
    public function category(){return $this->belongsTo(DownloadCategory::class,'category_id');}
    public function scopePublished($query){return $query->where('is_published', true)->whereHas('category', fn ($q) => $q->where('is_active', true));}
    public function getCoverUrlAttribute(): ?string{return $this->cover_path ? asset('storage/'.$this->cover_path) : null;}
    public function getFileSizeLabelAttribute(): string { $size=(float)$this->file_size; foreach(['B','KB','MB','GB'] as $unit){if($size<1024||$unit==='GB')return number_format($size,($size<10&&$unit!=='B')?1:0).' '.$unit;$size/=1024;} return ''; }
    public function getRouteKeyName(){return 'slug';}
    protected static function booted(): void { static::creating(function($item){if(!$item->slug)$item->slug=Str::slug($item->title).'-'.Str::random(5);}); }
}
