<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="icon" href="{{ asset(config('settings.theme')) }}/img/favicon.ico"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title }}</title>

    <link href="{{ asset(config('settings.theme')) }}/img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
    <link href="{{ asset(config('settings.theme')) }}/img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
    <link href="{{ asset(config('settings.theme')) }}/img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
    <link href="{{ asset(config('settings.theme')) }}/img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">
    <link href="{{ asset(config('settings.theme')) }}/img/favicon.png" rel="icon" type="image/png">
    <link href="{{ asset(config('settings.theme')) }}/img/favicon.ico" rel="shortcut icon">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- START CSS INCLUDE -->
    @yield('include_css_lib')
    <!-- END CSS INCLUDE -->
</head>
<body class="with-side-menu control-panel control-panel-compact animated fadeIn">

<header class="site-header">
    <div class="container-fluid">
        <a href="#" class="site-logo">
            <img class="hidden-md-down" src="{{ asset(config('settings.theme')) }}/img/logo-2.png" alt="">
            <img class="hidden-lg-up" src="{{ asset(config('settings.theme')) }}/img/logo-2.png" alt="">
        </a>

	        <span id="show-hide-sidebar" class="checkbox-toggle">
	            <input type="checkbox" id="show-hide-sidebar-toggle" checked>
	            <label for="show-hide-sidebar-toggle"></label>
	        </span>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>
        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="site-header-shown">
                    <button class="btn btn-nav btn-rounded btn-inline btn-info-outline" type="button" data-toggle="modal" data-target="#statsModal"">
                        Статистика
                    </button>
                    <a class="btn btn-nav btn-rounded btn-inline btn-default-outline" href="{{route("site.index")}}">
                        На сайт
                    </a>
                    <button type="button" data-toggle="modal" data-target="#addObj" class="btn btn-nav btn-rounded btn-inline btn-success-outline" href="{{route("object.create", ['category' => '4', 'deal' => 'Продажа', 'type' => 'хз'])}}">
                        Добавить объект
                    </button>
                    <a type="button" class="btn btn-nav btn-rounded btn-inline btn-warning-outline" href="{{route("admin.favorites")}}">
                        Избранное
                        <span class="badge badge-warning margin-left fav-count">{{$user->favorites()->count() + $user->a_favorites()->count()}}</span>
                    </a>
                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if(isset($user->image))
                                <img src="{{ asset(config('settings.theme')) }}/uploads/avatar/avatar-{{$user->id}}-64{{$user->image}}" alt="">
                            @else
                                <img src="{{ asset(config('settings.theme')) }}/img/avatar-2-64.png" alt="">
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="{{route("user.edit", ['user'=>$user->id])}}"><span class="font-icon glyphicon glyphicon-user"></span>Профиль</a>
                            <a class="dropdown-item" href="{{ route("settings.edit") }}"><span class="font-icon glyphicon glyphicon-cog"></span>Настройки</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route("logout")}}" onclick="event.preventDefault();
                            document.getElementById('logout').submit();"><span class="font-icon glyphicon glyphicon-log-out"></span>Выйти</a>
                            {!! Form::open(["url" => route('logout'), 'method' => "POST", "id" => "logout", "style" => "display: none;"]) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div><!--.site-header-shown-->
            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header><!--.site-header-->
<!-- Modal -->
<div class="modal fade" id="addObj" tabindex="-1" role="dialog" aria-labelledby="addObjLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="addObjLabel">Выберите тип объекта</h5>
            </div>
            <div class="modal-body clearfix no_padding">
                <div class="menu-td col-md-4 no_padding">
                    <div id="category" class="menu-block">
                        <div class="elem-nav-cat-title">Категория</div>
                        <div id="cat-kvart" data-type="1" data-show="cat-kvart-2" class="elem-nav-cat">Квартира</div>
                        <div id="cat-house" data-type="2" data-show="cat-house-2" class="elem-nav-cat">Дом, дача, участок</div>
                        <div id="cat-komn" data-type="3" data-show="cat-comnt-2" class="elem-nav-cat">Комната</div>
                    </div>
                </div>
                <div class="menu-td col-md-4 no_padding">
                    <div id="cat-sdelka" class="menu-block" style="display: none">
                        <div class="elem-nav-cat-title">Вид сделки</div>
                        <div data-deal="1" class="elem-nav-cat">Продажа</div>
                        <div data-deal="2" class="elem-nav-cat">Обмен</div>
                    </div>
                </div>
                <div id="menu-cat-3" class="menu-td col-md-4 no_padding">
                    <div id="cat-kvart-2" class="menu-block" style="display: none">
                        <div class="elem-nav-cat-title">Тип объекта</div>
                        <a href="" id="kvart-2-1"><div class="elem-nav-cat">Вторичка</div></a>
                        <a href="" id="kvart-2-2"><div class="elem-nav-cat">Новостройка</div></a>
                    </div>
                    <div id="cat-house-2" class="menu-block" style="display: none">
                        <div class="elem-nav-cat-title">Тип объекта</div>
                        <a href="" id="house-2-1"><div class="elem-nav-cat">Дом</div></a>
                        <a href="" id="house-2-2"><div class="elem-nav-cat">Дача</div></a>
                        <a href="" id="house-2-3"><div class="elem-nav-cat">Коттедж</div></a>
                        <a href="" id="house-2-4"><div class="elem-nav-cat">Таунхаус</div></a>
                    </div>
                    <div id="cat-comnt-2" class="menu-block" style="display: none">
                        <div class="elem-nav-cat-title">Тип объекта</div>
                        <a href="" id="comnt-2-1"><div id="comnt-2-1" class="elem-nav-cat">Гостиничного</div></a>
                        <a href="" id="comnt-2-2"><div class="elem-nav-cat">Коридорного</div></a>
                        <a href="" id="comnt-2-3"><div class="elem-nav-cat">Секционного</div></a>
                        <a href="" id="comnt-2-4"><div class="elem-nav-cat">Коммунальная</div></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mobile-menu-left-overlay"></div>
<!-- START MAIN NAVIGATION -->
@yield('navigation')
<!-- END MAIN NAVIGATION -->

<div class="page-content">
    <div class="container">
        @if (count($errors) > 0)
            <div class="form-error-text-block">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
<!-- START CONTENT -->
    @yield('content')
<!-- END CONTENT -->
    </div><!--.container-fluid-->
</div><!--.page-content-->
<!-- START JS INCLUDE -->
@yield('include_js_lib')
<!-- END JS INCLUDE -->
<style type="text/css">
#YMapsID {
    width: 100%;
    height: 400px;
}
</style>
<!-- START SCRIPT INCLUDE -->
@yield('include_js')
@if (session('status'))
    <script>
    $(document).ready(function() {
        $.notify({
            icon: 'font-icon font-icon-check-circle',
            title: '<strong>Успешно</strong>',
            message: '{{ session('status') }}'
        }, {
            type: 'success'
        });
    });
    </script>
@endif
@if (session('search_status'))
    <script>
        $(document).ready(function() {
            $.notify({
                icon: 'font-icon font-icon-check-circle',
                title: '<strong>Результат поиска:</strong>',
                message: 'Найдено {{ session('search_status') }} объекта.'
            }, {
                type: 'success'
            });
        });
    </script>
@endif
@if (session('error'))
    <script>
        $(document).ready(function() {
            $.notify({
                icon: 'ffont-icon font-icon-warning',
                title: '<strong>Ошибка</strong>',
                url: '{{session('url')}}',
                message: '{{session('error')}}'
            }, {
                type: 'danger',
                timer: 100000,
                url_target: '_blank'
            });
        });
    </script>
@endif
<!-- END SCRIPT INCLUDE -->
<script src="{{ asset(config('settings.theme')) }}/js/app.js"></script>
<script src="{{ url("js/script-".$str) }}"></script>
<script>
    $(document).ready(function () {

        $('#cat-kvart, #cat-house, #cat-komn').click(function(){
            var show = $('#cat-sdelka').show(0);
            $('#menu-cat-3 .menu-block').each(function () {
                var show = $(this).css("display");
                if (show == "block") {
                    $(this).hide(0);
                }
            });
        });
        $('#cat-sdelka .elem-nav-cat').click(function(){
            var id_target = $('#category .elem-nav-cat-active').attr('data-show');
            $('#menu-cat-3 .menu-block').each(function () {
                if ($(this).attr('id') == id_target) {
                    $(this).show(0);
                } else {
                    $(this).hide(0);
                }
            });
            88172        });

        $('.elem-nav-cat').click(function () {
            $(this).parent().find('.elem-nav-cat-active').removeClass('elem-nav-cat-active');
            $(this).addClass('elem-nav-cat-active');
        });


        $('#cat-sdelka .elem-nav-cat').click(function () {
            var type = $('#category .elem-nav-cat-active').attr('data-type');
            var deal = $(this).text();
            var site_address = "{{env('APP_URL')}}/";
            $('#kvart-2-1').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Вторичка");
            $('#kvart-2-2').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Новостройка");
            $('#house-2-1').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Дом");
            $('#house-2-2').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Дача");
            $('#house-2-3').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Коттедж");
            $('#house-2-4').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Таунхаус");
            $('#comnt-2-1').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Гостиничного");
            $('#comnt-2-2').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Коридорного");
            $('#comnt-2-3').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Секционного");
            $('#comnt-2-4').attr("href", site_address + "admin/object/create/" + type + "/" + deal + "/Коммунальная");

        });

    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.table tr').click(function () {
            var tab_height = $(this).find('.tab_content').height();
            if (tab_height == 70) {
                $(this).find('.tab_content').stop(true).animate({ height: "250px"}, 300)
            } else {
                $(this).find('.tab_content').stop(true).animate({ height: "70px"}, 300)
            }
        });
    });
</script>

<div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="statsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="statsModalLabel">Статистика</h5>
      </div>
      <div class="modal-body">

      <section class="tabs-section">
                <div class="tabs-section-nav tabs-section-nav-icons">
                    <div class="tbl">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab" aria-expanded="true">
                                    <span class="nav-link-in">
                                        Сегодня
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">
                                        Вчера
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tabs-1-tab-3" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">
                                        Всего
                                    </span>
                                </a>
                            </li>                        
                        </ul>
                    </div>
                </div><!--.tabs-section-nav-->

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active show" id="tabs-1-tab-1" aria-expanded="true">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Тип объекта</th>
                                <th>Квартиры</th>
                                <th>Дома</th>
                                <th>Комнаты</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Авито</td>
                                    <td>{{$Avito[1]}}</td>
                                    <td>{{$Avito[2]}}</td>
                                    <td>{{$Avito[3]}}</td>
                                    <td>{{$Avito[0]}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2" aria-expanded="false">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Тип объекта</th>
                                <th>Квартиры</th>
                                <th>Дома</th>
                                <th>Комнаты</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Авито</td>
                                    <td>{{$Avito[5]}}</td>
                                    <td>{{$Avito[6]}}</td>
                                    <td>{{$Avito[7]}}</td>
                                    <td>{{$Avito[4]}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-3" aria-expanded="false">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Тип объекта</th>
                                <th>Квартиры</th>
                                <th>Дома</th>
                                <th>Комнаты</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Авито</td>
                                    <td>{{$Avito[9]}}</td>
                                    <td>{{$Avito[10]}}</td>
                                    <td>{{$Avito[11]}}</td>
                                    <td>{{$Avito[8]}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
            </section>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function() {
        $("[data-popover=true]").webuiPopover({
            type: 'html',
            content: function () {
                var contentid = $(this).attr("data-popover-content");
                var remove = $(this).attr("data-checkbox");
                var content = $(contentid).clone(true).removeClass('hidden');
                if (!remove) {
                    $(contentid).remove();
                }
                return content;
            },
            onShow: function(e) {
                var show = e.attr("data-show");
                if (!show) {
                    e.attr("data-show", true);
                    var popid = e.attr("id");
                    var sliderid = $("[data-target=" + popid +"]").attr("data-slider-id");
                    initSlider(sliderid);
                }
            }
        });
        initSlider("init");
    });
</script>
</body>
</html>
