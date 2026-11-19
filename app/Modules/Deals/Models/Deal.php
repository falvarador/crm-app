<?php

namespace App\Modules\Deals\Models;

use App\Models\User;
use App\Modules\Activities\Models\Activity;
use App\Modules\Companies\Models\Company;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'company_id', 'contact_id', 'owner_id', 'value', 'amount', 'currency', 'stage',
    'expected_close_date', 'close_date', 'probability', 'badge', 'badge_variant', 'alert', 'critical', 'lost_reason',
])]
class Deal extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'probability' => 'integer',
            'critical' => 'boolean',
        ];
    }

    public function getAmountAttribute(): string
    {
        return (string) $this->value;
    }

    public function setAmountAttribute(int|float|string $value): void
    {
        $this->attributes['value'] = $value;
    }

    public function getCloseDateAttribute(): ?string
    {
        return $this->expected_close_date?->format('Y-m-d');
    }

    public function setCloseDateAttribute(?string $value): void
    {
        $this->attributes['expected_close_date'] = $value;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', ['ganada', 'perdida']);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%");
    }
}
