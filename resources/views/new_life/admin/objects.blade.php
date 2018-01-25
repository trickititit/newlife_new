    {!! $filter !!}


    <ul class="nav nav-tabs">
    @if($menus)
                @include(config('settings.theme').'.admin.objectsMenuItems',['items'=>$menus->roots(), "type" => $type])
    @endif
        {!! Form::select('order', $order_select, $selected, ["onchange" => "window.location.href=this.options[this.selectedIndex].value", "id" => "order"]) !!}
    </ul>
    <!-- Таблица -->
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th width="1">Сделка</th>
                    <th >Обьект</th>
                    <th >Адрес</th>
                    <th >Цена</th>
                    <th >Описание</th>
                    <th >Доплата</th>
                    <th >Комментарий</th>
                    <th >Контакты</th>
                    <th >Действия</th>
                </tr>
                </thead>
                <tbody>
    @if($objects)
    @foreach($objects as $object)
                <tr>
                    <td class="td-icon"><div class="tab_content">{!!  $object->deal == "Продажа" ? "<i class=\"fa fa-shopping-cart fa-lg\"></i>" : "<i class=\"fa fa-retweet fa-lg\"></i>" !!}</div></td>
                    <td class="table-obj"><div class="tab_content">
                        @if($object->category == 1)
                            <a href="{{route('site.object',['object'=>$object->alias])}}">{{$object->rooms}}-к квартира</a><br>{{$object->square}} м² {{$object->floor}}/{{$object->build_floors}} эт.<br>{{ $object->created_at->format('m/d/Y') }}
                        @elseif($object->category == 2)
                            <a href="{{route('site.object',['object'=>$object->alias])}}">{{$object->type}}</a><br>{{$object->home_square}} м² на участке {{$object->earth_square}}<br>{{ $object->created_at->format('m/d/Y') }}
                        @elseif($object->category == 3)
                            <a href="{{route('site.object',['object'=>$object->alias])}}">Комната в {{$object->rooms}}-к</a><br>{{$object->square}} м² {{$object->floor}}/{{$object->build_floors}} эт.<br>{{ $object->created_at->format('m/d/Y') }}
                        @endif
                        </div>
                    </td>
                    <td class="table-address"><div class="tab_content">{{ $object->address }},<br>{{ str_replace(array("микрорайон", "улица", "Квартал", "квартал", "поселок"), array("мкр", "ул", "кв-л", "кв-л", "п"), $object->raion->name) }}, <br> {{ $object->gorod->name }}</div></td>
                    <td><div class="tab_content">{{ number_format($object->price) }}</div></td>
                    <td class="table-desc"><div class="tab_content">{{ $object->desc }}</div></td>
                    <td><div class="tab_content">{{ number_format($object->surcharge) }}</div></td>
                    <td class="table-comment"><div class="tab_content">{{ $object->comment }}</div></td>
                    <td class="table-contact"><div class="tab_content">
                            @can("viewContacts", Auth::user())
                                @if(isset($object->working_id))
                                    @if($object->workingUser->id == $user->id)
                                        {{$object->client->phone}} {{ $object->client->name }} {{$object->client->father_name}}
                                    @else
                                        {{$object->workingUser->telefon}} {{ $object->workingUser->name }}
                                    @endif
                                @else
                                    {{$object->client->phone}} {{ $object->client->name }} {{$object->client->father_name}}
                                @endif
                            @else
                                {{$object->createdUser->telefon}} {{ $object->createdUser->name }}
                            @endcan
                        </div></td>
                    <td class="table-actions"><div class="tab_content"><div class="btn-actions centovka">
                            {!! $actions["object".$object->id] !!}
                         </div>
                        </div>
                    </td>
                </tr>
    @endforeach
    @endif
                </tbody>
            </table>
        <div class="pagina col-md-12">
            <nav>
                <ul class="pagination pagination-sm centovka">
                @if($objects->lastPage() > 1)
                    @if($objects->currentPage() !== 1)
                        <li class="page-item"><a class="page-link" href="{{ $objects->url(1) }}">&laquo;</a></li>
                        <li class="page-item"><a class="page-link" href="{{ $objects->url(($objects->currentPage() - 1)) }}">&lsaquo;</a></li>
                    @else
                        <li class="page-item disabled"><a class="page-link">&laquo;</a></li>
                        <li class="page-item disabled"><a class="page-link">&lsaquo;</a></li>
                    @endif
                    @if($objects->lastPage() > 2)
                        @if($objects->currentPage() == 1)
                            @for($i = 1; $i <= $objects->lastPage() && $i <= 3 ; $i++)
                                @if($objects->currentPage() == $i)
                                    <li class="page-item active"><a class="page-link">{{ $i }}</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $objects->url($i) }}">{{ $i }}</a></li>
                                @endif
                            @endfor
                        @else
                            @if($objects->currentPage() == $objects->lastPage())
                                @for($i = $objects->lastPage() - 2; $i <= $objects->lastPage() ; $i++)
                                    @if($objects->currentPage() == $i)
                                        <li class="page-item active"><a class="page-link">{{ $i }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $objects->url($i) }}">{{ $i }}</a></li>
                                    @endif
                                @endfor
                            @else
                                @for($i = $objects->currentPage() - 1; $i <= $objects->currentPage() + 1 ; $i++)
                                    @if($objects->currentPage() == $i)
                                        <li class="page-item active"><a class="page-link">{{ $i }}</a></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $objects->url($i) }}">{{ $i }}</a></li>
                                    @endif
                                @endfor
                            @endif
                        @endif
                    @else
                        @for($i = 1; $i <= $objects->lastPage(); $i++)
                            @if($objects->currentPage() == $i)
                                <li class="page-item active"><a class="page-link">{{ $i }}</a></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $objects->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor
                    @endif
                    @if($objects->currentPage() !== $objects->lastPage())
                        <li class="page-item"><a class="page-link" href="{{ $objects->url(($objects->currentPage() + 1)) }}">&rsaquo;</a></li>
                        <li class="page-item"><a class="page-link" href="{{ $objects->url($objects->lastPage()) }}">&raquo;</a></li>
                    @else
                            <li class="page-item disabled"><a class="page-link">&rsaquo;</a></li>
                        <li class="page-item disabled"><a class="page-link">&raquo;</a></li>
                    @endif
                @endif
                 </ul>
            </nav>
        </div>