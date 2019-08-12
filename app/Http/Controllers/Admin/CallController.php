<?php

namespace App\Http\Controllers\Admin;

use App\Call;
use App\Object;
use App\Repositories\CallRepository;
use App\Repositories\ObjectsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\FtpMegafon;
use Illuminate\Support\Facades\DB;

class CallController extends AdminController
{
    private $call_rep;


    public function __construct(ObjectsRepository $o_rep, CallRepository $call_rep)
    {
        parent::__construct(new \App\Repositories\AdmMenusRepository(new \App\AdmMenu), new \App\Repositories\SettingsRepository(new \App\Setting()), new \App\Repositories\AobjectsRepository(new \App\Aobject()), new \App\User);
        $this->o_rep = $o_rep;
        $this->call_rep = $call_rep;
    }
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function exists($url)
    {
        $results = DB::select('select * from calls where url = :url', ['url' => $url]);
        if($results) {
            return $results[0];
        } else {
            return false;
        }

    }

    public function index()
    {
        $ftp = new FtpMegafon("records.megapbx.ru", "direktor@ip-plehanov.megapbx.ru", "Gkt[fyjd_2019");
        $yesterday = Carbon::now()->subDay()->format('Y-m-d');
//        $contents = $ftp->getList($yesterday);
        $contents = $ftp->getListAll();
//        foreach ($contents as $content){
//            if ($content[0] == ".") continue;
//            $contents_ = $ftp->getList($content);
//            if($contents_) {
//                $calls = $this->parseCalls($contents_);
//                foreach ($calls as &$call) {
//                    foreach ($objects as $object) {
//                        if ($object->client->phone == $call["number"]) {
//                            $call["object_id"] = $object->id;
//                        }
//                    }
//                    $this->call_rep->Add($call);
//                }
//            }
//        }
            if($contents) {
                $objects = $this->o_rep->get();
                foreach ($objects as $object) {
                    $object->client = json_decode($object->client);
                    $phone = preg_replace("/[^,.0-9]/", '', $object->client->phone);
                    if ($phone[0] == 8 || $phone[0] == 7) {
                        $phone = substr($phone, 1);
                    }
                    $object->client->phone = $phone;
                }
                foreach ($contents as $content) {
                    if ($content[0] == ".") continue;
                    $contents_ = $ftp->getList($content);
                    if ($contents_) {
                        $calls = $this->parseCalls($contents_);
                        foreach ($calls as &$call) {
                            $ex_call = $this->exists($call["url"]);
                            if ($ex_call) {
                                $ex_call = Call::find($ex_call->id);
                                foreach ($objects as $object) {
                                    if ($object->client->phone == $ex_call->number) {
                                        $ex_call->object_id = $object->id;
                                        if ($ex_call->update()) {
                                            break;
                                        }
                                    }
                                }
                                continue;
                            } else {
                                foreach ($objects as $object) {
                                    if ($object->client->phone == $call["number"]) {
                                        $call["object_id"] = $object->id;
                                        break;
                                    }
                                }
                                dump($call);
                                $this->call_rep->Add($call);
                                continue;
                            }
                        }
                    }
                }
            }
    }


    private function parseCalls($ftp_calls) {
        $calls = array();
        foreach ($ftp_calls as $ftp_call) {
            if ($ftp_call[0] == ".") continue;
            $arr = explode("_", $ftp_call);
            $status = ($arr[1] == "in") ? 1 : 0;
            $data = explode("-", $arr[4]);
            $exec_data = Carbon::createFromFormat('Y-m-d H-i-s', $arr[2] . "-" . $arr[3] . "-" . $data[0] . " " . $data[1] . "-" . $arr[5] . "-" . $arr[6]);
            $phone = $phone = substr( $arr[7], 1);
            $url = $ftp_call;
            $calls[] = ["status" => $status, "exec_at" => $exec_data, "number" => $phone, "url" => $url, "object_id" => null];
        }
        return $calls;
    }


    public function getCall($data, $url) {
        $ftp = new FtpMegafon("records.megapbx.ru", "direktor@ip-plehanov.megapbx.ru", "Gkt[fyjd_2019");
        $file = $ftp->getFile($data, $url);
        $size   = $file['size']; // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte

        $headersArray=[
            'Accept-Ranges' => "bytes",
            'Accept-Encoding' => "gzip, deflate",
            'Pragma' => 'public',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => ' inline; filename='.$url,
            'Content-Length' => $size,
            'Content-Type' => "audio/mpeg",
            'Connection' => "Keep-Alive",
            'Content-Range' => 'bytes 0-' . $end . '/' . $size,
            'X-Pad' => 'avoid browser bug',
            'Etag' => 'mp3',
        ];

        return response($file['content'])->withHeaders($headersArray);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllInt($string) {
        $string = preg_replace("/[^0-9]/", '', $string);
        if ($string == "") $string = 0;
        return $string;
    }
}
