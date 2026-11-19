<?php

namespace App\Modules\Activities\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'type', 'subject', 'description', 'due_date', 'completed_at', 'schedule_label',
    'notes', 'completed', 'priority', 'user_id', 'activitable_type', 'activitable_id',
])]
class Activity extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getNotesAttribute(): ?string
    {
        return $this->description;
    }

    public function setNotesAttribute(?string $value): void
    {
        $this->attributes['description'] = $value;
    }

    public function getCompletedAttribute(): bool
    {
        return $this->completed_at !== null;
    }

    public function setCompletedAttribute(bool $value): void
    {
        $this->attributes['completed_at'] = $value ? now() : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activitable(): MorphTo
    {
        return $this->morphTo();
    }
}
