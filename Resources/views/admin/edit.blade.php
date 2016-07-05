@extends('admin::layouts.master')
@section('title'){{ trans('articles::admin.form.edit.header') }}@stop

@section('content')
    <div>
        @include('articles::admin.form', [
            'model' => $article, 
            'header' => trans('articles::admin.form.edit.header')
        ])
    </div>
@stop
