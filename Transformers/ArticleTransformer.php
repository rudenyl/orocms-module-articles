<?php
namespace Modules\Articles\Transformers;

use Hashids\Hashids;
use Modules\Articles\Entities\Article;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    /**
     * Enable ID hashing
     *
     * @var boolean
     */
    protected $hashed_id = true;

    /**
     * Enable/disable content in resource.
     *
     * @var boolean
     */
    protected $with_content = true;

    public function __construct()
    {
        $args = func_get_args();
        $options = array_shift($args);

        if (is_array($options)) {
            foreach ($options as $k => $v) {
                if (isset($this->$k)) {
                    $this->$k = $v;
                }
            }
        }
    }

    public function transform(Article $article)
    {
        $data = [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'description' => $article->description,
            'created_at' => (string)$article->created_at,
            'modified_at' => (string)$article->modified_at,
            'deleted_at' => (string)$article->deleted_at,
            'publish_up' => $article->publishing['publish_up'],
            'publish_down' => $article->publishing['publish_down'],
            'access' => $article->publishing['access'],
            'created_by' => $article->publishing['created_by'],
            'modified_by' => $article->publishing['modified_by'],
            'published' => isset($article->status) ? $article->status : $article->published
        ];

        // hashed id
        //$this->hashed_id and $data['id'] = $this->encode_id($data['id']);

        // with fulltext
        if (!$this->with_content) {
            unset($data['description']);
        }

        return $data;
    }
}