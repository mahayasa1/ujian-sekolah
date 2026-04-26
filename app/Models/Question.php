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
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Konversi URL Google Form biasa menjadi URL embed (viewform → viewform?embedded=true)
     */
    public function getEmbedUrlAttribute(): string
    {
        $url = $this->google_form_url;

        // Pastikan URL pakai ?embedded=true
        if (!str_contains($url, 'embedded=true')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'embedded=true';
        }

        return $url;
    }
}