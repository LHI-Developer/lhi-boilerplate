<?php

namespace App\Models;

/**
 * User Model Alias
 * 
 * This is a class alias that points to the actual User model in Core module.
 * This maintains backward compatibility with code/configs that reference App\Models\User.
 * 
 * The actual User model is located at: Modules\Core\Models\User
 */
class User extends \Modules\Core\Models\User
{
    // This class intentionally left empty.
    // All functionality is inherited from Modules\Core\Models\User
}
