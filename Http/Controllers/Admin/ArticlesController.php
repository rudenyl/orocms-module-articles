<?php
namespace Modules\Articles\Http\Controllers\Admin;

use OroCMS\Admin\Controllers\BaseController;
use Modules\Articles\Repositories\ArticleRepository;
use Modules\Articles\Validation\Create;
use Modules\Articles\Validation\Update;
use Modules\Articles\Events\ArticleEventHandler;
use Illuminate\Http\Request;

class ArticlesController extends BaseController
{
    protected $view_prefix = 'articles';

    /**
     * @var Modules\Articles\Entities\Article
     */
    protected $articles;

    /**
     * @param Modules\Articles\Repositories\ArticleRepository $repository
     */
    function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $articles = $this->repository->getAll();

        if ($request->isJson()) {
            return response()->json($articles);
        }

        return $this->view('admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return $this->view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Create $request)
    {
        try {
            $article = $this->repository->create();

            // redirect
            $next_uri = $request->get('next');

            // set response object
            $response = $this->redirect('articles.index');
            ($next_uri == 'self') and $response = $this->redirect('articles.item.edit', $article->id);

            return $response->withFlashMessage( trans('articles::admin.message.create.success') )
                ->withFlashType('info');
        }
        catch(\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withFlashMessage($e->getMessage())
                ->withFlashType('danger');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        try {
            $article = $this->repository->single($id);

            return $this->view('admin.edit', compact('article'));
        }
        catch (ModelNotFoundException $e) {
            return $this->view('admin.index');
        }
    }

    /**
     * Update the specified resource from PATCH method.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function patch(Request $request, $id = null)
    {
        $cids = $request->get('id') ?: [$id];

        try {
            $message = null;

            // restore
            if ($request->has('restore')) {
                $this->repository->patch('restore', $cids);
                $message = trans_choice('articles::admin.message.restore', count($cids));
            }
            // publishing only
            else if ($request->has('published')) {
                $this->repository->patch('published', $cids);
                $message = trans_choice('articles::admin.message.statuses', count($cids));
            }

            if ($request->ajax()) {
                return response()->json(compact('success', 'message'));
            }

            return $this->redirect('articles.index')
                ->withFlashMessage($message)->withFlashType('info');
        }
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('articles.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function update(Update $request, $id)
    {
        try {
            //
            // update
            //
            $this->repository->update($id);

            $params = [
                'success' => true,
                'message' => trans('articles::admin.message.update.success'),
                'next_uri' => $request->get('next')
            ];

            if ($request->ajax()) {
                return response()->json($params);
            }

            // set response object
            $response = $this->redirect('articles.index');
            ($params['next_uri'] == 'self') and $response = redirect()->back();

            return $response->withFlashMessage($params['message'])->withFlashType('info');
        }
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('articles.index')
                ->withFlashMessage($e->getMessage())->withFlashType('danger');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy(Request $request, $id = null)
    {
        try {
            // prioritize input over slug
            $cids = $request->get('id') ?: [$id];

            // force delete?
            $force_delete = $request->has('force_delete') and (int)$request->has('force_delete');
            $this->repository->delete($cids, $force_delete);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans_choice('articles::admin.message.' . ($force_delete ? 'deleted' : 'marked_deleted'), count($cids))
                ]);
            }

            return $this->redirect('admin.index');
        }
        catch (ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            return $this->redirect('admin.index');
        }
    }
}
