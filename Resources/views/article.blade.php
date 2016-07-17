@extends('theme::_layouts.base')

@if (isset($article))
    @section('title'){{ $article->title }}@stop

    @section('content')
        <h1>
            {{ $article->title }}
        </h1>

        {!! $article->description !!}
    @stop
@endif
