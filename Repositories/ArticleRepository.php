<?php
namespace Modules\Articles\Repositories;

use Modules\Articles\Entities\Article;
use Modules\Articles\Entities\ArticlePublishing;
use Modules\Articles\Transformers\ArticleTransformer;
use OroCMS\Admin\Contracts\EntityRepositoryInterface;
use League\Fractal\Manager;
use League\Fractal\Resource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Query\Expression;

class ArticleRepository implements EntityRepositoryInterface
{
    /**
     * @var integer
     */
    protected $perPage;

    /**
     * @var array
     */
    protected $default_data = [
        'defaults' => [
            'published' => 0
        ],
        'publishing' => [
            'access' => 0
        ]
    ];

    /**
     * Get model.
     *
     * @param boolean
     *
     * @return \Modules\Articles\Entities\Article
     */
    public function getModel($with_trashed = false)
    {
        $model = Article::with('publishing')
            ->getModel();

        if (Request::has('deleted')) {
            $with_trashed = (int)Request::get('deleted', $with_trashed);
        }

        $with_trashed and $model->withTrashed();

        return $model;
    }

    /**
     * Store data.
     *
     * @param array
     */
    public function create(array $data = null)
    {
        empty($data) and $data = array_merge($this->default_data['defaults'], Request::all());

        // slug clean-up
        $data['slug'] = Str::slug( Arr::get($data, 'slug') ?: $data['title'] );

        // create now!
        $article = $this->getModel()->create($data);

        // create publishing
        $publishing_data = array_merge(
            $this->default_data['publishing'],
            Request::get('publishing', []),
            ['created_by' => auth()->user()->id]
        );
        $article->publishing()
            ->updateOrCreate(['article_id' => $article->id], $publishing_data);

        return $article;
    }

    /**
     * Add data patches.
     *
     * @param array
     */
    public function patch($key, $value)
    {
        // restore
        if ($key == 'restore') {
            $this->getModel()->whereIn('id', $value)
                ->restore();
        }

        // publishing
        else if ($key == 'published') {
            $this->getModel()->whereIn('id', $value)
                ->update(Request::only($key));
        }

        return true;
    }

    /**
     * Update article.
     *
     * @param array
     */
    public function update($id)
    {
        $data = array_merge($this->default_data['defaults'], Request::all());

        // slug clean-up
        $data['slug'] = Str::slug( Arr::get($data, 'slug') ?: $data['title'] );

        $article = $this->findById($id, true);

        // update publishing
        $publishing_data = array_merge(
            $this->default_data['publishing'],
            Request::get('publishing', []),
            ['modified_by' => auth()->user()->id]
        );
        $article->publishing()
            ->updateOrCreate(['article_id' => $article->id], $publishing_data)
            ->touch();

        // update now!
        $article->update($data);

        return $article;
    }

    /**
     * Delete article.
     *
     * @param integer
     * @param boolean
     */
    public function delete($cids, $force_delete = false)
    {
        $results = $this->getModel(true)->whereIn('id', $cids);
        $force_delete ? $results->forceDelete() : $results->delete();

        return true;
    }

    /**
     * Return item collection.
     *
     * @return mixed
     */
    public function getAll()
    {
        #
        # get options
        #
        $page = (int)Request::get('page', 1);
        $sort = Request::get('sort', 'created_at');
        $sort_dir = Request::get('order', 'desc');

        // build
        $repository = $this->articleQuery($sql_publishing);

        // publishing
        $published = Request::get('published', null);
        if (!is_null($published)) {
            $repository->where(new Expression($sql_publishing), $published);
        }

        // has search context
        $search = Request::get('search', false);
        if ($search) {
            $repository->where('title', 'LIKE', "%{$search}%");
        }

        // deleted only
        if (Request::get('deleted_only', false)) {
            $repository->onlyTrashed();
        }

        $total = $repository->count();
        $articles = $repository
            ->orderBy($sort, $sort_dir)
            ->take($this->perPage())
            ->skip($this->perPage() * ($page - 1));

        //
        // serialize
        //
        $resource = new Resource\Collection($articles->get(), new ArticleTransformer([
            'with_content' => false,
            'hashed_id' => true
        ]));
        $rows = (new Manager())->createData($resource)->toArray();

        // get listing count
        $count = count($resource->getData());

        return compact('total', 'count') + $rows;
    }

    /**
     * Get active articles
     *
     * @return array
     */
    public function active()
    {
        $repository = $this->articleQuery($sql_publishing);

        return $repository->where(new Expression($sql_publishing), 1)
            ->get();
    }

    /**
     * Get article item by id.
     *
     * @param integer
     *
     * @return mixed
     */
    public function findById($id, $with_trashed = false)
    {
        $article = $this->getModel($with_trashed)->findorFail($id);

        return $article;
    }

    /**
     * Get single entry.
     *
     * @param integer
     *
     * @return mixed
     */
    public function single($id)
    {
        $article = $this->articleQuery()
            ->where('articles.id', $id)
            ->first();

        if (empty($article)) {
            abort(404);
        }

        return $article;
    }

    /**
     * Get article item by key.
     *
     * @param string
     * @param mixed
     * @param string
     *
     * @return mixed
     */
    public function findBy($key, $value, $operator = '=')
    {
        // build
        $repository = $this->articleQuery($sql_publishing);

        // published only
        $repository->where(new Expression($sql_publishing), 1);

        // find
        $article = $repository
            ->where($key, $operator, $value)
            ->first();

        if (empty($article)) {
            abort(404);
        }

        // is private?
        if ($article->publishing->access && !auth()->user()) {
            abort(401);
        }

        return $article;
    }

    /**
     * Get page listing count.
     *
     * @return integer
     */
    public function perPage()
    {
        $perPage = (int)Request::get('limit', $this->perPage);
        $perPage = $perPage ?: config('articles.pages.article.perpage', 10);

        return $perPage;
    }

    /**
     * Return paginated listing
     *
     * @param mixed
     *
     * @return mixed
     */
    public function paginate($data)
    {
        return $data->paginate( $this->perPage() );
    }

    /**
     * Query builder
     *
     * Returns formatted result
     */
    private function articleQuery(&$sql_publishing = '')
    {
        // build
        $sql_publishing = "
            CASE WHEN
                (
                    `articles`.published = '1' AND
                    (`article_publishing`.publish_up IS NULL OR `article_publishing`.publish_up <= now()) AND
                    (`article_publishing`.publish_down IS NULL OR `article_publishing`.publish_down >= now())
                ) THEN 1
                WHEN (`articles`.published = '1'
                        AND `article_publishing`.publish_up <> '0000-00-00 00:00:00'
                        AND `article_publishing`.publish_up IS NOT NULL
                        AND `article_publishing`.publish_up >= now()) THEN 2
                WHEN (`articles`.published = '1'
                        AND `article_publishing`.publish_down <> '0000-00-00 00:00:00'
                        AND `article_publishing`.publish_down IS NOT NULL
                        AND `article_publishing`.publish_down <= now()) THEN 3
                ELSE `articles`.published
            END
        ";
        $query = $this->getModel()
            #
            # article data first
            #
            ->select(new Expression("articles.*, ({$sql_publishing}) as status"))

            #
            # with(['article_publishing' => Closure]) won't fetch properties on empty data
            #
            ->leftJoin('article_publishing', 'article_publishing.article_id', '=', 'articles.id');

        return $query;
    }
}
