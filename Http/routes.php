<?php
/**
 * Routes
 */

Route::group(['namespace' => 'Modules\Articles\Http\Controllers', 'middleware' => 'web'], function() {
    Route::group(['prefix' => config('modules.configs.articles.route.prefix', 'articles')], function() {
        Route::get('/', ['as' => 'articles.index', 'uses' => 'ArticlesController@index']);
        Route::get('/{slug}', ['as' => 'articles.show', 'uses' => 'ArticlesController@show']);
    });

    /**
     * Admin routes
     */
    Route::group([
            'as' => 'admin.articles.',
            'namespace' => 'Admin',
            'prefix' => config('modules.configs.articles.route.cp', 'articles'),
            'middleware' => config('admin.filter.auth')], function() {

        Route::get('/', ['as' => 'index', 'uses' => 'ArticlesController@index']);

        // items
        Route::group(['prefix' => 'item'], function() {
            Route::get('/create', ['as' => 'item.create', 'uses' => 'ArticlesController@create']);
            Route::get('/{article}/edit', ['as' => 'item.edit', 'uses' => 'ArticlesController@edit']);
            Route::post('/', ['as' => 'item.store', 'uses' => 'ArticlesController@store']);
            Route::put('/{article}', ['as' => 'item.update', 'uses' => 'ArticlesController@update']);
            Route::patch('/{article?}', ['as' => 'item.patch', 'uses' => 'ArticlesController@patch']);
            Route::delete('/{article?}', ['as' => 'item.destroy', 'uses' => 'ArticlesController@destroy']);
        });
    });
});
