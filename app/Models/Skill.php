<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'candidate_skills')->withTimestamps();
    }

    public static function findOrCreateByName(string $name): self
    {
        $normalized = trim($name);
        $slug = Str::slug($normalized);

        return static::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $normalized]
        );
    }
}
