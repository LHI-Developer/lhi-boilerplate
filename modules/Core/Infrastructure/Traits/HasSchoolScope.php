<?php

namespace Modules\Core\Infrastructure\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * HasSchoolScope Trait
 * 
 * Implements multi-tenancy for SIT LHI by automatically scoping
 * queries to the current school context.
 * 
 * Usage:
 * - Add 'school_id' column to your table migration
 * - Use this trait in your model
 * - All queries will automatically filter by current school
 */
trait HasSchoolScope
{
    /**
     * Boot the HasSchoolScope trait for a model.
     *
     * @return void
     */
    protected static function bootHasSchoolScope(): void
    {
        // Auto-assign school_id when creating
        static::creating(function (Model $model) {
            if (!$model->isDirty('school_id') && static::hasSchoolContext()) {
                $model->setAttribute('school_id', static::getCurrentSchoolId());
            }
        });

        // Global scope untuk semua query
        static::addGlobalScope('school', function (Builder $builder) {
            if (static::hasSchoolContext()) {
                $builder->where($builder->getQuery()->from . '.school_id', static::getCurrentSchoolId());
            }
        });
    }

    /**
     * Check if school context is available.
     *
     * @return bool
     */
    protected static function hasSchoolContext(): bool
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Check if user has school_id attribute/relationship
        return isset($user->school_id) && !is_null($user->school_id);
    }

    /**
     * Get current school ID from context.
     *
     * @return int|null
     */
    protected static function getCurrentSchoolId(): ?int
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        // Return school_id from authenticated user
        // Assumes User model has 'school_id' column or relationship
        return $user->school_id ?? null;
    }

    /**
     * Scope query to specific school.
     *
     * @param Builder $query
     * @param int $schoolId
     * @return Builder
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope query to exclude school filter (for admin/superadmin).
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutSchoolScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('school');
    }
}
