<?php
namespace Modules\Articles\Http\Controllers;

use Modules\Articles\Repositories\ArticleRepository;
use OroCMS\Admin\Controllers\FrontendBaseController as BaseController;

class ArticlesController extends BaseController
{
    protected $route_prefix = 'articles';
    protected $view_prefix = 'articles';

    protected $repository;

    function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $articles = $this->repository->active();

        return $this->view('index', compact('articles'));
    }

    public function show($slug)
    {
        $article = $this->repository->findBy('slug', $slug)->first();
        $view = $this->view('article', compact('article'));

        #
        # onAfterRenderItem
        #
        event('articles.onAfterRenderItem', $view);

        return $view;
    }
}
