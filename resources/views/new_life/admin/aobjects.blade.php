<div class="col-md-12">
    {!! $filter !!}
</div>

<div class="col-md-12">
        <ul class="nav nav-tabs">
        {!! Form::select('order', $order_select, $selected, ["onchange" => "window.location.href=this.options[this.selectedIndex].value", "id" => "order"]) !!}
    </ul>
    <!-- Таблица -->
    <div class="tab-content">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="30">Обьект</th>
                    <th width="40">Адрес</th>
                    <th width="10">Цена</th>
                    <th >Описание</th>
                    <th width="100">Контакты</th>
                    <th width="80">Действия</th>
                </tr>
                </thead>
                <tbody>
                @if($objects)
                    @foreach($objects as $object)
                        <tr>
                            <td>
                                @if($object->category == 1)
                                    <a href="{{$object->link}}" target="_blank">{{$object->rooms}}-к квартира</a><br>{{$object->square}} м² {{$object->floor}}/{{$object->build_floors}} эт.<br>{{ ($object->date != null)? $object->date->format('m-d-Y H:i'): "" }}
                                @elseif($object->category == 2)
                                    <a href="{{$object->link}}" target="_blank">{{$object->type}}</a><br>{{$object->home_square}} м² на участке {{$object->earth_square}}<br>{{ ($object->date != null)? $object->date->format('m-d-Y H:i'): "" }}
                                @elseif($object->category == 3)
                                    <a href="{{$object->link}}" target="_blank">Комната в {{$object->rooms}}-к</a><br>{{$object->square}} м² {{$object->floor}}/{{$object->build_floors}} эт.<br>{{ ($object->date != null)? $object->date->format('m-d-Y H:i'): "" }}
                                @endif
                            </td>
                            <td>{{ $object->city }},<br>{{ $object->address }},<br>{{ $object->area }}</td>
                            <td>{{ number_format($object->price) }}</td>
                            <td>{{ $object->desc }}</td>
                            <td>{{$object->client_contacts}} - {{ $object->client_name }}</td>
                            <td width="100"><div class="btn-actions centovka">
                            {!! $actions["object".$object->id] !!}
                         </div>
                    </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
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
    </div>

</div>