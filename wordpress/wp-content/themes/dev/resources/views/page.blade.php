@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
  @include('partials.default.page-header')
  @include('partials.default.content-page')
  @endwhile
@endsection
