<div class="box-typical box-typical-padding">
        <h1>Избранное</h1>
    <section class="tabs-section">
        <div class="tabs-section-nav tabs-section-nav-icons">
            <div class="tbl">
                <ul class="nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab" aria-expanded="true">
                                    <span class="nav-link-in">
                                        Новая жизнь
                                    </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">
                                        Авито
                                    </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div><!--.tabs-section-nav-->

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="tabs-1-tab-1" aria-expanded="true">
                @foreach($favorites as $object)
                    <div class="row margin_fav block_fav">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                @if($object->images->isNotEmpty())
                                    @foreach($object->images as $image)
                                        <img src="{{ asset(config('settings.theme')) }}/uploads/images/{{$image->object_id}}/{{$image->new_name}}" class="img-responsive">
                                        @break
                                    @endforeach
                                @else
                                    <img src="{{ asset(config('settings.theme')) }}/img/no-images.jpg" class="img-responsive">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($object->category == 1)
                                            <a href="{{route('site.object',['object'=>$object->alias])}}">{{$object->rooms}}-к квартира</a>
                                        @elseif($object->category == 2)
                                            <a href="{{route('site.object',['object'=>$object->alias])}}">{{$object->type}}</a>
                                        @elseif($object->category == 3)
                                            <a href="{{route('site.object',['object'=>$object->alias])}}">Комната в {{$object->rooms}}-к</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 margin_fav">
                                        <span>{{ number_format($object->price) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 margin_fav" style="color: #C1C1C1">
                                        {{ $object->address }}, {{ str_replace(array("микрорайон", "улица", "Квартал", "квартал", "поселок"), array("мкр", "ул", "кв-л", "кв-л", "п"), $object->raion->name) }}, {{ $object->gorod->name }}<br>
                                        {{ $object->created_at->format('m/d/Y') }}<br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                    <form action='{{route('object.favorite',['object'=>$object->alias])}}' method='post'><input type="hidden" name="_method" value="PUT"><input type="hidden" name="_token" value="{{csrf_token()}}"><input type="hidden" name="type" value="fulldelete"><button class='btn btn-danger' type='submit' title='Убрать'>Убрать</button></form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div><!--.tab-pane-->
            <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2" aria-expanded="false">
                @foreach($a_favorites as $object)
                    <div class="row margin_fav block_fav">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                    <img src="{{ asset(config('settings.theme')) }}/img/no-images.jpg" class="img-responsive">
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($object->category == 1)
                                            <a href="{{$object->link}}" target="_blank">{{$object->rooms}}-к квартира</a>
                                        @elseif($object->category == 2)
                                            <a href="{{$object->link}}" target="_blank">{{$object->type}}</a>
                                        @elseif($object->category == 3)
                                            <a href="{{$object->link}}" target="_blank">Комната в {{$object->rooms}}-к</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 margin_fav">
                                        <span>{{ number_format($object->price) }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 margin_fav" style="color: #C1C1C1">
                                        {{ $object->address }}, {{ str_replace(array("микрорайон", "улица", "Квартал", "квартал", "поселок"), array("мкр", "ул", "кв-л", "кв-л", "п"), $object->area) }}, {{ $object->city }}<br>
                                        {{ $object->created_at->format('m/d/Y') }}<br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <form action='{{route('object.favorite',['aobject'=>$object->id])}}' method='post'><input type="hidden" name="_method" value="PUT"><input type="hidden" name="_token" value="{{csrf_token()}}"><input type="hidden" name="type" value="fulldelete"><button class='btn btn-danger' type='submit' title='Убрать'>Убрать</button></form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div><!--.tab-pane-->
        </div><!--.tab-content-->
    </section>
</div>