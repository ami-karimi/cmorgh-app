<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $fillable = ['title', 'platform', 'protocol', 'content', 'attachments', 'is_published'];


    protected $casts = [
        'attachments'  => 'array',
        'is_published' => 'boolean',
    ];

    public function getAttachmentsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // اگر رکورد قدیمی به صورت تک رشته بود آن را داخل آرایه برگردان
        return [$value];
    }

}
