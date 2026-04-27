<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'google_form_url',
        'google_form_edit_url',
        'google_sheet_url',
        'description',
        'duration',
        'exam_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'exam_date'  => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEmbedUrlAttribute(): string
    {
        $url = $this->google_form_url;
        if (!str_contains($url, 'embedded=true')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'embedded=true';
        }
        return $url;
    }
}