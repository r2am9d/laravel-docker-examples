<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    public static function booted()
    {
        self::creating(function ($model) {
            $model->id = Str::ulid();
        });
    }
}
