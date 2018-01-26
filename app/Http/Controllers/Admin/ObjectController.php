<?php

namespace App\Http\Controllers\Admin;

use App\Object;
use App\Aobject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ObjectsRepository;
use App\Repositories\CitiesRepository;
use App\Repositories\AreasRepository;
use App\Repositories\ComfortsRepository;
use App\Components\JavaScriptMaker;
use App\Components\ExcelExport;
use App\Http\Requests\ObjectRequest;
use Gate;
use Carbon\Carbon;
use File;

class ObjectController extends AdminController
{
    public $o_rep;
    public $city_rep;
    public $area_rep;
    public $com_rep;

    public function __construct(ObjectsRepository $o_rep, CitiesRepository $city_rep, AreasRepository $area_rep, ComfortsRepository $com_rep) {
       parent::__construct(new \App\Repositories\AdmMenusRepository(new \App\AdmMenu), new \App\Repositories\SettingsRepository(new \App\Setting()), new \App\Repositories\AobjectsRepository(new \App\Aobject()), new \App\User);

//        if(Gate::denies('VIEW_ADMIN')) {
//            abort(403);
//        }
        $this->template = config('settings.theme').'.admin.index';
        $this->o_rep = $o_rep;
        $this->city_rep = $city_rep;
        $this->area_rep = $area_rep;
        $this->com_rep = $com_rep;
        $this->inc_css_lib = array_add($this->inc_css_lib,'dropzone', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/dropzone.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,'jq-steps', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/separate/vendor/jquery-steps.min.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,'multi-org', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/lib/multipicker/multipicker.min.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,'multi-custom', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/separate/vendor/multipicker.min.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,'chosen', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/chosen.min.css">'));
        $this->inc_js_lib = array_add($this->inc_js_lib,'dropzone',array('url' => '<script src="'.$this->pub_path.'/js/dropzone.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib,'jq-validate', array('url' => '<script src="'.$this->pub_path.'/js/lib/jquery-validation/jquery.validate.min.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib,'jq-steps', array('url' => '<script src="'.$this->pub_path.'/js/lib/jquery-steps/jquery.steps.min.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'y-maps', array('url' => '<script src="//api-maps.yandex.ru/2.0/?lang=ru-RU&load=package.full"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'adr_se', array('url' => '<script src="'.$this->pub_path.'/js/search_address.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'multipicker', array('url' => '<script src="'.$this->pub_path.'/js/lib/multipicker/multipicker.min.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'jq-input-mask', array('url' => '<script src="'.$this->pub_path.'/js/lib/input-mask/jquery.mask.min.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'init-input-mask', array('url' => '<script src="'.$this->pub_path.'/js/lib/input-mask/input-mask-init.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib, 'chosen', array('url' => '<script src="'.$this->pub_path.'/js/chosen.jquery.min.js"></script>'));
        // INIT INPUTS
        $this->inputs = array_add($this->inputs, "obj_type", array("1" => "Квартира", "2" => "Дом, Дача, Таунхаус", "3" => "Комната"));
        $this->inputs = array_add($this->inputs, "obj_deal", array("Продажа" => "Продажа", "Обмен" => "Обмен"));
        $this->inputs = array_add($this->inputs, "obj_form_1", array("Вторичка" => "Вторичка", "Новостройка" => "Новостройка"));
        $this->inputs = array_add($this->inputs, "obj_form_2", array("Дом" => "Дом", "Дача" => "Дача", "Коттедж" => "Коттедж", "Таунхаус" => "Таунхаус"));
        $this->inputs = array_add($this->inputs, "client_need", array("1-к квартира" => "1-к квартира", "2-к квартира" => "2-к квартира", "3-к квартира" => "3-к квартира", "4-к квартира" => "4-к квартира", "Комната" => "Комната", "Дом" => "Дом", "Дача" => "Дача", "Коттедж" => "Коттедж", "Таунхаус" => "Таунхаус"));
        $this->inputs = array_add($this->inputs, "obj_form_3", array("Гостиничного" => "Гостиничного", "Коридорного" => "Коридорного", "Секционного" => "Секционного", "Коммунальная" => "Коммунальная"));
        $this->inputs = array_add($this->inputs, "obj_room", array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "9+"));
        $this->inputs = array_add($this->inputs, "obj_home_floors_2", array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5+"));
        $this->inputs = array_add($this->inputs, "obj_build_type_1", array("Кирпичный" => "Кирпичный", "Панельный" => "Панельный", "Блочный" => "Блочный", "Монолитный" => "Монолитный", "Деревянный" => "Деревянный"));
        $this->inputs = array_add($this->inputs, "obj_build_type_2", array("Кирпич" => "Кирпич", "Брус" => "Брус", "Бревно" => "Бревно", "Металл" => "Металл", "Пеноблоки" => "Пеноблоки", "Сендвич-панели" => "Сендвич-панели", "Ж/б панели" => "Ж/б панели", "Экспериментальные материалы" => "Экспериментальные материалы"));
        $this->inputs = array_add($this->inputs, "obj_floor", array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10", "11" => "11", "12" => "12", "13" => "13", "14" => "14", "15" => "15", "16" => "16", "17" => "16+"));
        $this->inputs = array_add($this->inputs, "obj_distance", array("0" => "В черте города", "10" => "10 км", "20" => "20 км", "30" => "30 км", "50" => "50 км", "70" => "70+ км"));
        $this->inputs = array_add($this->inputs, "obj_home_floors_1", array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10", "11" => "11", "12" => "12", "13" => "13", "14" => "14", "15" => "15", "16" => "16", "17" => "16+"));
        $this->inputs = array_add($this->inputs, "obj_general_square", array("30" => "30", "32" => "32", "36" => "36", "44" => "44", "60" => "60", "66" => "66", "72" => "72", "84" => "84"));
        $this->inputs = array_add($this->inputs, "obj_square_kitchen", array("6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10", "12" => "12", "14" => "14", "16" => "16"));
        $this->inputs = array_add($this->inputs, "obj_square_life", array("15" => "15", "16" => "16", "18" => "18", "19" => "19", "20" => "20", "22" => "22", "30" => "30", "40" => "40"));
    }

    public function index() {
        $content = File::get(base_path("storage/objects.json"));
        $json = json_decode($content);
        foreach ($json as $obj) {
//            dump($obj);
            $this->o_rep->addObject($obj);
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(JavaScriptMaker $jsmaker, $category, $deal, $type)
    {
        $this->checkUser();
        $obj_param = view(config('settings.theme').'.admin.objectParam')->with(array('category' => $category, 'deal' => $deal, 'type' => $type));
        $cities = $this->city_rep->get();
        $obj_city = array();
        foreach ($cities as $city) {
            $obj_city = array_add($obj_city, $city->id, $city->name );
            $obj_area = array();
            foreach ($city->areas as $area) {
                $obj_area = array_add($obj_area, $area->id, $area->name );
            }
            $this->inputs = array_add($this->inputs, "obj_area".$city->id, $obj_area);
        }
        $this->inputs = array_add($this->inputs, "obj_city", $obj_city);
        $comforts = $this->com_rep->get();
        $jsmaker->setJs("obj-create", "", true, csrf_token(), $this->randStr);
        $rand_obj_id = rand(1,1000);
        $this->content = view(config('settings.theme').'.admin.objectCreate')->with(array('cities' => $cities, "obj_id" => $rand_obj_id, "comforts" => $comforts, "inputs" => $this->inputs, 'obj_param' => $obj_param, 'category' => $category, 'deal' => $deal, 'type' => $type))->render();
        $this->title = 'Создание нового объекта';
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ObjectRequest $request)
    {
        $this->checkUser();
        $result = $this->o_rep->addObject($request);
        if(is_array($result) && (!empty($result['error']) || !empty($result['errors']))) {
            return back()->with($result);
        }

        return redirect('/admin')->with($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(JavaScriptMaker $jsmaker, Object $object)
    {
        $this->checkUser();
        if($this->user->cant('update', $object)) {
            return back()->with(array('error' => 'Доступ запрещен'));
        }
        $obj_param = view(config('settings.theme').'.admin.objectParam')->with(array('category' => $object->category, 'deal' => $object->deal, 'type' => $object->type));
        $cities = $this->city_rep->get();
        $obj_city = array();
        foreach ($cities as $city) {
            $obj_city = array_add($obj_city, $city->id, $city->name );
            $obj_area = array();
            foreach ($city->areas as $area) {
                $obj_area = array_add($obj_area, $area->id, $area->name );
            }
            $this->inputs = array_add($this->inputs, "obj_area".$city->id, $obj_area);
        }
        $this->inputs = array_add($this->inputs, "obj_city", $obj_city);
        $comforts = $this->com_rep->get();
        $jsmaker->setJs("obj-edit", $object, true, csrf_token(), $this->randStr);
        $object->client = json_decode($object->client);
        if ($object->client->phone[0] == "8") {
            $object->client->phone = substr( $object->client->phone, 1);
        }
        $this->content = view(config('settings.theme').'.admin.objectCreate')->with(array("object" => $object,'cities' => $cities, "obj_id" => $object->id, "comforts" => $comforts, "inputs" => $this->inputs, 'obj_param' => $obj_param, 'category' => '', 'deal' => '', 'type' => ''))->render();
        $this->title = 'Редактирование объекта';
        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Object $object)
    {
        $this->checkUser();
        if($this->user->cant('update', $object)) {
            return back()->with(array('error' => 'Доступ запрещен'));
        }
        $result = $this->o_rep->updateObject($request, $object);
        if(is_array($result) && !empty($result['error'])) {
            return back()->with($result);
        }

        return redirect('/admin')->with($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //ACTIONS

    public function destroy(Object $object)
    {
        $this->checkUser();
        if($this->user->cant('delete', $object)) {
            return back()->with(array('error' => 'Доступ запрещен'));
        }
        if ($object->forceDelete()) {
            return back()->with(['status' => 'Объект удален']);
        } else {
            return back()->with(['error' => 'Ошибка удаления']);
        }
    }

    public function InPrework(Object $object)
    {
        $this->checkUser();
        $object->preworkingUser()->associate($this->user);
        if ($object->update()) {
            return back()->with(['status' => 'Объект добавлен в работу']);
        } else {
            return back()->with(['error' => 'Ошибка добавления в работу']);
        }
    }

    public function AccessPrework(Object $object)
    {
        $this->checkUser();
        $user = $object->preworkingUser;
        $object->worked_at = Carbon::now();
        $object->workingUser()->associate($user);
        $object->preworkingUser()->dissociate();
        if ($object->update()) {
            return back()->with(['status' => 'Объект принят в работу']);
        } else {
            return back()->with(['error' => 'Ошибка принятия в работу']);
        }
    }

    public function CancelPrework(Object $object)
    {
        $this->checkUser();
        $object->preworkingUser()->dissociate();
        if ($object->update()) {
            return back()->with(['status' => 'Объект принят в работу']);
        } else {
            return back()->with(['error' => 'Ошибка принятия в работу']);
        }
    }

    public function Unwork(Object $object)
    {
        $this->checkUser();
        $object->workingUser()->dissociate();
        if ($object->update()) {
            return back()->with(['status' => 'Объект убран из работы']);
        } else {
            return back()->with(['error' => 'Ошибка удаления из работы']);
        }
    }

    public function CheckCompleted(Object $object){
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $objects = $object->InWorkAll()->get();
        foreach ($objects as $object) {
            if (!isset($object->activate_state)) {
                $object->activate_state = 0;
            }
            if($object->worked_at->addMonths(1 + $object->activate_state) < Carbon::now()) {
                dump($object->worked_at->addMonths(1 + $object->activate_state));
                $object->activate_state++;
                $user = $object->working_id;
                $object->completedUser()->associate($user);
                $object->update();
            }
        }
    }

    public function Activate(Object $object)
    {
        $this->checkUser();
        $object->activate_at = Carbon::now();
        $object->completedUser()->dissociate();
        $state = $object->activate_state;
        if ($state != null) {
            $object->activate_state = ++$state;
        } else {
            $object->activate_state = 1;
        }
        if ($object->update()) {
            return back()->with(['status' => 'Объект активирован']);
        } else {
            return back()->with(['error' => 'Ошибка активацции']);
        }
    }

    public function Restore(Object $object)
    {
        $this->checkUser();
        $object->deletedUser()->dissociate();
        $object->update();
        if ($object->restore()) {
            return back()->with(['status' => 'Объект восстановлен']);
        } else {
            return back()->with(['error' => 'Ошибка восстановления']);
        }
    }

    public function softDelete(Object $object)
    {
        $this->checkUser();
        if($this->user->cant('softdelete', $object)) {
            return back()->with(array('error' => 'Доступ запрещен'));
        }
        $object->deletedUser()->associate($this->user);
        $object->update();
        if ($object->delete()) {
            return back()->with(['status' => 'Объект удален']);
        } else {
            return back()->with(['error' => 'Ошибка удаления']);
        }
    }
    
    public function export(ExcelExport $excel, Request $request) {
        $this->checkUser();
        if($request->user != "") {
            $objects = $this->o_rep->get("*", false, false, array("created_id", $request->user));
        } else {
            $objects = $this->o_rep->get();
        }
        $excel->Export($objects, $this->user->login);
        $path = storage_path().'/app/public/'.env('THEME','default').'/xlsx/'.$this->user->login.'.xlsx';
        if (file_exists($path)) {
            return response()->download($path);
        }
    }

    public function AobjDelete(Aobject $aobject)
    {
        $this->checkUser();
        if ($aobject->delete()) {
            return back()->with(['status' => 'Объект удален']);
        } else {
            return back()->with(['error' => 'Ошибка удаления']);
        }
    }

    public function ShowPhone(Object $object) {
//        сделать проверочки
        $object->client = json_decode($object->client);
        return response()->json([
            'id'   => $object->id,
            'name' => $object->client->name,
            'phone' => preg_replace("/[^,.0-9]/", '', $object->createdUser->telefon)
        ]);

//        @can("viewContacts", Auth::user())
//                                @if(isset($object->working_id))
//            @if($object->workingUser->id == $user->id)
//        {{$object->client->phone}} {{ $object->client->name }} {{$object->client->father_name}}
//                                    @else
//                                        {{$object->workingUser->telefon}} {{ $object->workingUser->name }}
//                                    @endif
//                                @else
//                                    {{$object->client->phone}} {{ $object->client->name }} {{$object->client->father_name}}
//                                @endif
//                            @else
//                                {{$object->createdUser->telefon}} {{ $object->createdUser->name }}
//                            @endcan
    }
    
    
}
