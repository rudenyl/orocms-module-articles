<?php
namespace Modules\Articles\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded  = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The fillable property.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'summary', 'description', 'published'];

    /**
     * Softdelete attribute.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Return `status` field if exists for published column
     */
    public function getPublishedAttribute()
    {
        if (isset($this->attributes['status'])) {
            return (int)$this->attributes['status'];
        }

        return (int)$this->attributes['published'];
    }

    /**
     * Scope "published".
     *
     * @param mixed $query
     *
     * @return mixed
     */
    public function scopepublished($query)
    {
        return $query->where('published', 1)
            ->where(function($query) {
                if (!auth()->check()) {
                    $query->where('access', 0);
                }
            });
    }

    /**
     * Publishing data
     */
    public function publishing()
    {
        return $this->hasOne(ArticlePublishing::class);
    }
}
