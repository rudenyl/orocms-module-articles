@extends('theme::_layouts.base')

@if (isset($articles))
    @section('title')
        Articles
    @stop

    @section('content')
        <h1>
            List of Articles
        </h1>

        @if(isset($articles))
        <ul>
        @foreach($articles as $article)
            <li>
                <a href="{{ route('articles.show', $article['slug']) }}" target="_blank">{{ $article['title'] }}</a>
            </li>
        @endforeach
        </ul>
        @else
        <p>
            The dog run away with the list. Bad dog!
        </p>
        @endif
    @stop
@endif
