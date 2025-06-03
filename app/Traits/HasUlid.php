<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    public static function booted(): void
    {
        self::creating(function ($model): void {
            $model->id = mb_strtolower((string) Str::ulid());
        });
    }
}
