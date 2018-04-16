<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Object;
use App\Post;
use Storage;
use App\Repositories\ObjectsRepository;
use App\Repositories\CitiesRepository;
use App\Repositories\AreasRepository;
use App\Repositories\AobjectsRepository;
use Illuminate\Support\Facades\Session;
use App\Components\JavaScriptMaker;
use Carbon\Carbon;
use Menu;
use Gate;
use URL;
use Route;

class IndexController extends SiteController
{
    public function __construct(ObjectsRepository $o_rep, CitiesRepository $city_rep, AreasRepository $area_rep, AobjectsRepository $aobj_rep) {
        parent::__construct(new \App\Repositories\AdmMenusRepository(new \App\AdmMenu), new \App\Repositories\SettingsRepository(new \App\Setting), new \App\Object);

//        if(Gate::denies('VIEW_ADMIN')) {
//            abort(403);
//        }

        $this->inc_css_lib = array_add($this->inc_css_lib,  'jq-ui', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/lib/jqueryui/jquery-ui.min.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,  'da-slider', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/site.slider.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,  'bx-slider', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/jquery.bxslider.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,  'hover', array('url' => '<link rel="stylesheet" href="'.$this->pub_path.'/css/hover.css">'));
        $this->inc_css_lib = array_add($this->inc_css_lib,  'modernizr', array('url' => '<script src="'.$this->pub_path.'/js/modernizr.custom.28468.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib,    'bx-slider', array('url' => '<script src="'.$this->pub_path.'/js/jquery.bxslider.min.js"></script>'));
        $this->inc_js_lib = array_add($this->inc_js_lib,    'cs-slider', array('url' => '<script src="'.$this->pub_path.'/js/jquery.cslider.js"></script>'));
        $this->template = config('settings.theme').'.index';
        $this->o_rep = $o_rep;
        $this->city_rep = $city_rep;
        $this->aobj_rep = $aobj_rep;
        $this->area_rep = $area_rep;
    }

    public function index(JavaScriptMaker $jsmaker, Post $post, Object $object) {
        $this->title = "Агенство недвижимости Новая Жизнь";
        $inwork = $object->InWorkAll()->take(10)->get();
        $posts = $post->OnMain()->get();
        $faq_posts = $post->FAQ()->get();
        $this->content = view(config('settings.theme').'.front')->with(['posts' => $posts, 'faq' => $faq_posts, "inwork" => $inwork]);
        $jsmaker->setJs("front", "", ($this->spec_offer_count > 5)? false : true, "", $this->randStr);
        return $this->renderOutput();
    }

    public function parseAvito(Request $request, JavaScriptMaker $jsmaker) {
        $jsmaker->setJs("parse-avito", $request->parse_url, true, "", $this->randStr);
        $cmd = "phantomjs ".base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        dump($output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoK(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/volgogradskaya_oblast_volzhskiy/kvartiry/prodam/vtorichka?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoKA(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/srednyaya_ahtuba/kvartiry/prodam/vtorichka?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoH(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/volgogradskaya_oblast_volzhskiy/doma_dachi_kottedzhi/prodam?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoHA(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/srednyaya_ahtuba/doma_dachi_kottedzhi/prodam?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoC(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/volgogradskaya_oblast_volzhskiy/komnaty/prodam?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function curlAvitoCA(JavaScriptMaker $jsmaker) {
        if (request()->ip() != "193.124.189.57"){
            abort(404);
        }
        $url = "https://m.avito.ru/srednyaya_ahtuba/komnaty/prodam?user=1";
        $jsmaker->setJs("parse-avito", $url, true, "", $this->randStr);
        $cmd = 'phantomjs '.base_path("phantomjs/bin/avito.js");
        exec($cmd, $output);
        $this->objectAvitoToBase($output, $jsmaker);
    }

    public function checkArray($array, $type) {
        if (!preg_match("/\\d\\-к/", $array[1 + $type]) && $array[1 + $type] != "Студия") {
            array_splice($array, 1 + $type, 1);
            return $this->checkArray($array, $type);
        }
        return $array;
    }

    function isStart($str, $substr)
    {
        $result = strpos($str, $substr);
        if ($result === 0) { // если содержится, начиная с первого символа
            return true;
        } else {
            return false;
        }
    }

    public function objectAvitoToBase($objects, $jsmaker){
        $result = ["success" => 0, "error" => 0,"have" => 0, "object_s" => "", "object_e" => "", "object_h" => ""];
        $text = "";
        $i = 0;
        foreach ($objects as $object_) {
            if(!$this->isStart($object_, "{")) continue;
            $parseobject = json_decode($object_);
            if ($this->aobj_rep->getOne($parseobject->id)) {
                continue;
            }
            $req = [$parseobject->title, $parseobject->url];
            $jsmaker->setJs("parse-avito-page", $req, true, "", $this->randStr);
            $cmd = "phantomjs ".base_path("phantomjs/bin/avito.js");
            exec($cmd, $outputs);
            $object_avito = "";
            foreach ($outputs as $output) {
                if(!$this->isStart($object_, "{")) continue;
                $object_avito = $output;
            }
            $object = json_decode($object_avito);
            $object->category = mb_strtolower($object->category);
            if ($object->category == "квартиры") {
                $object->category = 1;
            } elseif ($object->category == "комнаты") {
                $object->category = 3;
            } elseif ($object->category == "дома, дачи, коттеджи") {
                $object->category = 2;
            }
            $object->date = $this->parseDate($object->date);
//            $object->title_obj = explode(" ", $object->title_obj);
            switch ($object->category) {
                case '1':
                    if ($object->title_obj[1] == "вновостройке") {
                        $object->type = "Новостройка";
                        $type = 1;
                    } else {
                        $object->type = "Вторичка";
                        $type = 0;
                    }
                    $object->title_obj = $this->checkArray($object->title_obj, $type);
                    $object->rooms = $this->findParamOnString($object->title_obj, $object->category, "room", $type);
                    $object->square = $this->findParamOnString($object->title_obj, $object->category, "square", $type);
                    $object->floor = $this->findParamOnString($object->title_obj, $object->category, "floor", $type);
                    $object->build_floors = $this->findParamOnString($object->title_obj, $object->category, "build_floors", $type);
                    $object->deal = $this->findParamOnString($object->title_obj, $object->category, "deal", $type);
                    $object->price = $this->findParamOnString($object->price, $object->category, "price", $type);
                    $object->build_type = $this->findParamOnString($object->title_obj, $object->category, "build_type", $type);
                    $object->id = $this->findParamOnString($object->id, $object->category, "id", $type);
                    $object->url = "http://avito.ru".$object->url;
                    $object->area = $this->findParamOnString($object->city, $object->category, "area", $type);
                    $object->city = $this->findParamOnString($object->city, $object->category, "city", $type);
                    $result_ = $this->aobj_rep->addObj($object);
                    if ($result_ == "one") {
                        $result["have"]++;
                        $result["object_h"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } elseif ($result_) {
                        $result["success"]++;
                        $result["object_s"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } else {
                        $result["error"]++;
                        $result["object_e"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    }
                    break;
                case '2':
                    $object->type = $this->findParamOnString($object->title_obj, $object->category, "type");
                    $object->distance = $this->findParamOnString($object->title_obj, $object->category, "distance");
                    $object->home_square = $this->findParamOnString($object->title_obj, $object->category, "home_square");
                    $object->earth_square = $this->findParamOnString($object->title_obj, $object->category, "earth_square");
                    $object->build_floors = $this->findParamOnString($object->title_obj, $object->category, "build_floors");
                    $object->deal = $this->findParamOnString($object->title_obj, $object->category, "deal");
                    $object->price = $this->findParamOnString($object->price, $object->category, "price");
                    $object->build_type = $this->findParamOnString($object->title_obj, $object->category, "build_type");
                    $object->id = $this->findParamOnString($object->id, $object->category, "id");
                    $object->url = "http://avito.ru".$object->url;
                    $object->area = $this->findParamOnString($object->city, $object->category, "area");
                    $object->city = $this->findParamOnString($object->city, $object->category, "city");
                    $result_ = $this->aobj_rep->addObj($object);
                    if ($result_ == "one") {
                        $result["have"]++;
                        $result["object_h"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } elseif ($result_) {
                        $result["success"]++;
                        $result["object_s"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } else {
                        $result["error"]++;
                        $result["object_e"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    }
                    break;
                case '3':
                    $object->type = "";
                    $object->rooms = $this->findParamOnString($object->title_obj, $object->category, "room");
                    $object->square = $this->findParamOnString($object->title_obj, $object->category, "square");
                    $object->floor = $this->findParamOnString($object->title_obj, $object->category, "floor");
                    $object->build_floors = $this->findParamOnString($object->title_obj, $object->category, "build_floors");
                    $object->deal = $this->findParamOnString($object->title_obj, $object->category, "deal");
                    $object->price = $this->findParamOnString($object->price, $object->category, "price");
                    $object->build_type = $this->findParamOnString($object->title_obj, $object->category, "build_type");
                    $object->id = $this->findParamOnString($object->id, $object->category, "id");
                    $object->url = "http://avito.ru".$object->url;
                    $object->area = $this->findParamOnString($object->city, $object->category, "area");
                    $object->city = $this->findParamOnString($object->city, $object->category, "city");
                    $result_ = $this->aobj_rep->addObj($object);
                    if ($result_ == "one") {
                        $result["have"]++;
                        $result["object_h"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } elseif ($result_) {
                        $result["success"]++;
                        $result["object_s"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    } else {
                        $result["error"]++;
                        $result["object_e"] .= "\\nlink = ". $object->url. " id = ". $object->id;
                    }
                    break;
                default:
                    # code...
                    break;
            }
            $text .= json_encode($object, JSON_UNESCAPED_UNICODE);
            $i++;
        }
        $text .= "\\n";
        $text .= implode(",", $result);
        Storage::disk('phantom')->put('avito.txt', $text);
        Session::flash('parse_success', $result["success"]);
        Session::flash('parse_error', $result["error"]);
    }

    public function findParamOnString($string, $category, $param, $type = 0) {
        $search_build_types = ["кирпичного", "панельного", "блочного", "монолитного", "деревянного"];
        $build_types = ["Кирпичный", "Панельный", "Блочный", "Монолитный", "Деревянный"];
        $search_types = ["дом", "дачу", "коттедж", "таунхаус"];
        $types = ["Дом", "Дача", "Коттедж", "Таунхаус"];
        $search_build_types_2 = ["кирпич", "брус", "бревно", "газоблоки", "металл", "пеноблоки", "сэндвич-панели", "ж/б панели", "экспериментальные материалы"];
        $build_types_2 = ["Кирпич", "Брус", "Бревно", "Металл", "Газоблоки", "Пеноблоки", "Сендвич-панели", "Ж/б панели", "Экспериментальные материалы"];
        switch ($category) {
            case '1':
                switch ($param) {
                    case 'id':
                        return $this->getAllInt($string);
                        break;
                    case 'room':
                        if ($string[1 + $type] == "Студия") {
                            return 1;
                        }
                        $room = explode(" ", $string[1 + $type]);
                        for($i = 1; $i < 11; $i++) {
                            if ($room[0] == "$i-к") {
                                return $i;
                            }
                        }
                        break;
                    case 'square':
                        $square = explode(" ", $string[2 + $type]);
                        return (int)$square[0];
                        # code...
                        break;
                    case 'floor':
                        $floor = explode(" ", $string[3 + $type]);
                        return (int)$floor[1];
                        # code...
                        break;
                    case 'build_floors':
                        for($i = 1; $i < 22; $i++) {
                            if (preg_match("~".$i."\\-этажного~", $string[3 + $type])) {
                                return $i;
                            }
                        }
                        # code...
                        break;
                    case 'build_type':
                        for ($i = 0; $i < count($search_build_types); $i++) {
                            if (preg_match("~".$search_build_types[$i]."~", $string[3 + $type])) {
                                return $build_types[$i];
                            }
                        }
                        # code...
                        break;
                    case "deal":
                        $deal = explode(" ", $string[0]);
                        return $deal[0];
                        break;
                    case "city":
                        $city = explode(",", $string);
                        return trim($city[1]);
                        break;
                    case "area":
                        $area = explode(",", $string);
                        return isset($area[2]) ? trim($area[2]) : "";
                        break;
                    case 'price':
                        return $this->getAllInt($string);
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case '2':
                switch ($param) {
                    case 'id':
                        return $this->getAllInt($string);
                        break;
                    case 'type':
                        for ($i = 0; $i < count($search_types); $i++) {
                            if (preg_match("~".$search_types[$i]."~", $string[1])) {
                                return $types[$i];
                            }
                        }
                        break;
                    case 'home_square':
                        if (preg_match("~\\d* м²~", $string[2], $matches)) {
                            return $this->getAllInt($matches[0]);
                        }
                        break;
                    case 'earth_square':
                        return $this->getAllInt($string[4]);
                        break;
                    case 'build_floors':
                        for ($i = 1; $i < 11; $i++) {
                            if (preg_match("/.*(".$i.".*\-этажный|".$i."\-этажный).*/", $string[2])) {
                                return $i;
                            }
                        }
                        break;
                    case 'distance':
                        if($string[5] == ",в черте города") {
                            return 0;
                        } else {
                            return $this->getAllInt($string[5]);
                        }
                        break;
                    case 'build_type':
                        for ($i = 0; $i < count($search_build_types_2); $i++) {
                            if (preg_match("~".$search_build_types_2[$i]."~", $string[3])) {
                                return $build_types_2[$i];
                            }
                        }
                        break;
                    case "deal":
                        return $string[0];
                        break;
                    case "city":
                        $city = explode(",", $string);
                        return trim($city[1]);
                        break;
                    case "area":
                        $area = explode(",", $string);
                        return isset($area[2]) ? trim($area[2]) : "";
                        break;
                    case 'price':
                        return $this->getAllInt($string);
                        break;
                    default:
                        break;
                }
                break;
            case '3':
                switch ($param) {
                    case 'id':
                        return $this->getAllInt($string);
                        break;
                    case 'room':
                        $room = explode(" ", $string[2]);
                        for($i = 1; $i < 11; $i++) {
                            if ($room[0] == "в$i-к") {
                                return $i;
                            }
                        }
                        break;
                    case 'square':
                        $square = explode(" ", $string[1]);
                        return (int)$square[1];
                        # code...
                        break;
                    case 'floor':
                        $floor = explode(" ", $string[3]);
                        return (int)$floor[1];
                        # code...
                        break;
                    case 'build_floors':
                        for($i = 1; $i < 22; $i++) {
                            if (preg_match("~".$i."\\-этажного~", $string[3])) {
                                return $i;
                            }
                        }
                        # code...
                        break;
                    case 'build_type':
                        for ($i = 0; $i < count($search_build_types); $i++) {
                            if (preg_match("~".$search_build_types[$i]."~", $string[3])) {
                                return $build_types[$i];
                            }
                        }
                        # code...
                        break;
                    case "deal":
                        $deal = explode(" ", $string[0]);
                        return $deal[0];
                        break;
                    case "city":
                        $city = explode(",", $string);
                        return trim($city[1]);
                        break;
                    case "area":
                        $area = explode(",", $string);
                        return isset($area[2]) ? trim($area[2]) : "";
                        break;
                    case 'price':
                        return $this->getAllInt($string);
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                # code...
                break;
            default:
                # code...
                break;
        }
    }

    public function parseDate($date){
        $monthsList = array(
            "1"=>"января","2"=>"февраля","3"=>"марта",
            "4"=>"апреля","5"=>"мая", "6"=>"июня",
            "7"=>"июля","8"=>"августа","9"=>"сентября",
            "10"=>"октября","11"=>"ноября","12"=>"декабря");
        preg_match("~\\d\\d\\:\\d\\d~", $date, $time);
        $time = explode(":", $time[0]);
        if(preg_match("~сегодня~", $date)) {
            $obj_date = Carbon::today();
            $obj_date->hour($time[0]);
            $obj_date->minute($time[1]);
        } elseif (preg_match("~вчера~", $date)) {
            $obj_date = Carbon::yesterday();
            $obj_date->hour($time[0]);
            $obj_date->minute($time[1]);
        } else {
            foreach ($monthsList as $key => $value) {
                if (preg_match("~".$value."~", $date)) {
                    $mounth = $key;
                }
            }
            preg_match("~ \\d* ~", $date, $day);
            $day = (int)$day[0];
            $now = Carbon::now();
            $year = $now->year;
            $obj_date = Carbon::createFromFormat('Y-m-d H:i', "$year-$mounth-$day ".$time[0].":".$time[1]);
        }
        return $obj_date;
    }

    public function getAllInt($string) {
        $string = preg_replace("/[^0-9]/", '', $string);
        if ($string == "") $string = 0;
        return $string;
    }
}