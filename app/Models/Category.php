<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Collection<int, Post> $posts
 * @property-read int|null $posts_count
 */
final class Category extends Model
{
    use HasUlid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the posts of the category
     *
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
