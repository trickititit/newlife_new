@if($items)
    @foreach($items as $item)
        <li class="nav-item">
            <a class="nav-link {{ $type == $item->type ? "active" : '' }}" href="{{$item->url()}}">{{$item->title}}<span class="badge badge-default margin-left">{{$item->count}}</span></a>
        </li>
    @endforeach
@endif
