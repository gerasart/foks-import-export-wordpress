<!doctype html>
<html {!! get_language_attributes() !!}>
@include('partials.default.head')
<body @php body_class() @endphp>
@php do_action('get_header') @endphp
@include('partials.default.header')

<div class="wrap" role="document">
  <div class="content">
    <main class="main">
      @yield('content')
    </main>
    @if (App\display_sidebar())
      <aside class="sidebar">
        @include('partials.sidebar.sidebar')
      </aside>
    @endif
  </div>
</div>

@if (isset($_GET['debug']) && $_GET['debug'] === 'contr')
  @hierarchy
@elseif (isset($_GET['debug']) && $_GET['debug'] === 'vars')
  @debug
  {{--@vars--}}
@elseif (isset($_GET['debug']) && $_GET['debug'] === 'all')
  @hierarchy
  @debug
@endif

@php do_action('get_footer') @endphp
@include('partials.default.footer')

@php wp_footer() @endphp
</body>
</html>