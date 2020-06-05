@extends('layouts.app')

@section('content')
  @include('partials.default.page-header')

  @if (!have_posts())
    <div class="alert alert-warning">
      {{ __('Sorry, no results were found.', 'sage') }}
    </div>
    {!! get_search_form(false) !!}
  @endif

  @while(have_posts()) @php the_post() @endphp
    @include('partials.default.content-search')
  @endwhile

  {!! get_the_posts_navigation() !!}
@endsection
