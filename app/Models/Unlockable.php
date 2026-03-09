<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unlockable extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'name',
        'description',
        'category',
        'unlock_condition',
    ];

    public function userUnlockables(): HasMany
    {
        return $this->hasMany(UserUnlockable::class, 'unlockable_key', 'key');
    }
}
