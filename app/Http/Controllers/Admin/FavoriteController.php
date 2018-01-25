<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Object;
use App\AObject;

class FavoriteController extends AdminController
{
    protected $c_rep;

    public function __construct() {
        parent::__construct(new \App\Repositories\AdmMenusRepository(new \App\AdmMenu), new \App\Repositories\SettingsRepository(new \App\Setting()), new \App\Repositories\AobjectsRepository(new \App\Aobject()), new \App\User);
        $this->template = config('settings.theme').'.admin.index';
    }

    public function index() {
        $this->checkUser();
        $favorites = $this->user->favorites()->get();
        $a_favorites = $this->user->a_favorites()->get();
        $this->content = view(config('settings.theme').'.admin.favorites')->with(array("favorites" => $favorites, "a_favorites" => $a_favorites))->render();
        $this->title = 'Избранное';
        return $this->renderOutput();
    }

    public function Favorite(Request $request, Object $object){
        $this->checkUser();
        if($request->type == "add"){
            $this->user->favorites()->attach($object->id);
            $this->user->update();
        } else if ($request->type == "delete") {
            $this->user->favorites()->detach($object->id);
            $this->user->update();
        }else if ($request->type == "fulldelete") {
            $this->user->favorites()->detach($object->id);
            $this->user->update();
            return back();
        } else {
            return false;
        }
        return response()->json([
            'id' => $object->id
        ]);
    }

    public function AFavorite(Request $request, AObject $object){
        $this->checkUser();
        if($request->type == "add"){
            $this->user->a_favorites()->attach($object->id);
            $this->user->update();
        } else if ($request->type == "delete") {
            $this->user->a_favorites()->detach($object->id);
            $this->user->update();
        }else if ($request->type == "fulldelete") {
            $this->user->a_favorites()->detach($object->id);
            $this->user->update();
            return back();
        } else {
            return false;
        }
        return response()->json([
            'id' => $object->id
        ]);
    }
}
