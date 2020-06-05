<header class="banner">
  <div class="container">
    <a class="brand" href="{{ home_url('/') }}">{{ get_bloginfo('name', 'display') }}</a>
    <nav class="nav-primary">
        @if(Theme\Help::Menu())
            <ul class="header_menu">
                @foreach(\Theme\Help::Menu() as $item)
                    @php $current = ( $item->object_id == get_the_id() ) ? 'current' : ''; @endphp
                    <li class="header_menu-item {{$current}}">
                        <a class="nav__link" href="{{$item->url}}" title="{{$item->title}}">{{$item->title}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </nav>
  </div>
</header>
