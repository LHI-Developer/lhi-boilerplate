<?php

namespace Modules\Core\Services;

use Modules\Core\Models\School;
use Illuminate\Database\Eloquent\Collection;

/**
 * TenantService
 * 
 * Manages multi-tenancy logic for the SIT LHI system.
 * Handles school context switching and tenant data isolation.
 */
class TenantService
{
    /**
     * Get all active schools.
     *
     * @return Collection<int, School>
     */
    public function getAllActiveSchools(): Collection
    {
        return School::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find school by ID.
     *
     * @param int $schoolId
     * @return School|null
     */
    public function findSchoolById(int $schoolId): ?School
    {
        return School::find($schoolId);
    }

    /**
     * Find school by code.
     *
     * @param string $code
     * @return School|null
     */
    public function findSchoolByCode(string $code): ?School
    {
        return School::where('code', $code)->first();
    }

    /**
     * Create a new school.
     *
     * @param array $data
     * @return School
     */
    public function createSchool(array $data): School
    {
        return School::create($data);
    }

    /**
     * Update school information.
     *
     * @param int $schoolId
     * @param array $data
     * @return bool
     */
    public function updateSchool(int $schoolId, array $data): bool
    {
        $school = $this->findSchoolById($schoolId);

        if (!$school) {
            return false;
        }

        return $school->update($data);
    }

    /**
     * Activate a school.
     *
     * @param int $schoolId
     * @return bool
     */
    public function activateSchool(int $schoolId): bool
    {
        return $this->updateSchool($schoolId, ['is_active' => true]);
    }

    /**
     * Deactivate a school.
     *
     * @param int $schoolId
     * @return bool
     */
    public function deactivateSchool(int $schoolId): bool
    {
        return $this->updateSchool($schoolId, ['is_active' => false]);
    }

    /**
     * Check if a school code is available.
     *
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function isSchoolCodeAvailable(string $code, ?int $excludeId = null): bool
    {
        $query = School::where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }
}
