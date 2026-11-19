<?php

namespace App\Modules\Contacts\Models;

use App\Models\User;
use App\Modules\Activities\Models\Activity;
use App\Modules\Companies\Models\Company;
use App\Modules\Deals\Models\Deal;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'first_name', 'last_name', 'email', 'phone', 'position',
    'role', 'lead_source', 'source', 'status', 'owner_id', 'role_tag', 'notes',
])]
class Contact extends Model
{
    use SoftDeletes;

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getRoleAttribute(): ?string
    {
        return $this->position;
    }

    public function setRoleAttribute(?string $value): void
    {
        $this->attributes['position'] = $value;
    }

    public function getSourceAttribute(): ?string
    {
        return $this->lead_source;
    }

    public function setSourceAttribute(?string $value): void
    {
        $this->attributes['lead_source'] = $value;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}
