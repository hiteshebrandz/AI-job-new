<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'industry',
        'company_size',
        'founded',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public static function resolveForJob(Job $job): self
    {
        $slug = Str::slug($job->company_name);

        return self::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $job->company_name,
                'description' => 'Human Capital Intelligence',
                'industry' => 'HR Tech',
                'company_size' => '500+ Employees',
                'founded' => '2016',
            ]
        );
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }
}
