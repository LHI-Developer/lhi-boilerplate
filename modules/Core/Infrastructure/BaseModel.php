<?php

namespace Modules\Core\Infrastructure;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model for SIT LHI Modular Boilerplate
 * 
 * All module models should extend this base model to inherit
 * common functionality and behaviors.
 */
abstract class BaseModel extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Get the fillable attributes for the model.
     *
     * @return array<int, string>
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }
}
