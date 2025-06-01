<?php

declare(strict_types=1);

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\HasUlid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @property string $id
 * @property string|null $thumbnail
 * @property string $title
 * @property string $color
 * @property string $slug
 * @property string|null $content
 * @property string|null $tags
 * @property bool $is_published
 * @property string $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Category $category
 */
final class Post extends Model
{
    use HasUlid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'posts';

    protected $casts = [
        'is_published' => 'bool',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
