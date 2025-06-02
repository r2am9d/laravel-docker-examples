<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUlid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Post
 *
 * @property string $id
 * @property string|null $thumbnail
 * @property string $title
 * @property string $color
 * @property string $slug
 * @property string|null $content
 * @property array<array-key, mixed>|null $tags
 * @property bool $is_published
 * @property string $category_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Category $category
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 */
final class Post extends Model
{
    use HasUlid;

    public $incrementing = false;

    protected $table = 'posts';

    protected $keyType = 'string';

    protected $casts = [
        'is_published' => 'bool',
        'tags' => 'array',
    ];

    protected $fillable = [
        'thumbnail',
        'title',
        'color',
        'slug',
        'content',
        'tags',
        'is_published',
        'category_id',
    ];

    /**
     * Get the category of the post
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the users of the post
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'posts_users')
            ->withPivot(['order'])
            ->withTimestamps();
    }
}
