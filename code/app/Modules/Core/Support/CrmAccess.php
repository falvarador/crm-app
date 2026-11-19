<?php

namespace App\Modules\Core\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class CrmAccess
{
    public static function scope(Builder $query, User $user, string $ownerColumn = 'owner_id'): Builder
    {
        return $user->role === 'admin' ? $query : $query->where($ownerColumn, $user->id);
    }

    public static function companyExists(User $user): Exists
    {
        return Rule::exists('companies', 'id')->where(function ($query) use ($user): void {
            $query->whereNull('deleted_at');
            if ($user->role !== 'admin') {
                $query->where('owner_id', $user->id);
            }
        });
    }

    public static function ownerExists(User $user): Exists
    {
        return Rule::exists('users', 'id')->where(function ($query) use ($user): void {
            if ($user->role !== 'admin') {
                $query->where('id', $user->id);
            }
        });
    }
}
