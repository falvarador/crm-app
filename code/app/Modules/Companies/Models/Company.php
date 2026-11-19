<?php

namespace App\Modules\Companies\Models;

use App\Models\User;
use App\Modules\Activities\Models\Activity;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Deals\Models\Deal;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'tax_id', 'tier', 'status', 'sector', 'industry', 'location', 'address',
    'website', 'phone', 'email', 'owner_id', 'mrr', 'mrr_plan',
])]
class Company extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return ['mrr' => 'decimal:2'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
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
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('tax_id', 'like', "%{$term}%");
        });
    }
}
