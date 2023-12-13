<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Plant;
use Illuminate\Support\Facades\Auth;

class SecondController extends Controller
{
    private $AIOKey;
    private $username;

    public function __construct()
    {
        $this->AIOKey = env('AIOKEY');
        $this->username = env('AUSER');
    }

    public function AllGroup(Request $request)
    {
        try {

            $userid = Auth()->user()->id;
            $groups = DB::table('plants')->select('name', 'groupkey')->where('user_id', $userid)->get();

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'groups' => $groups
            ]);
            
        } catch (RequestException $error) {
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null,
            ], 500);
        }
    }



    public function GroupFeed(Request $request)
    {
        try {
            $Key = $request->input('GroupKey');
            $client = new Client();
            $response = $client->get('http://io.adafruit.com/api/v2/' . $this->username . '/groups/'. $Key, [
                'headers' => [
                    'X-AIO-Key' => $this->AIOKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'groups' => collect($data['feeds'])->map(function ($item) {
                    return [
                        'idfeed' => $item['id'],
                        'feed' => $item['name'],
                        'key' => $item['key']
                    ];
                })->all(),
            ]);
            
        } catch (RequestException $error) {
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null,
            ], 500);
        }
    }















    public function CreateGroup(Request $request)
    {
        try {
            $name = $request->input('name');

            $userid = Auth()->user()->id;
            DB::table('plants')->insert([
                'name' => $name,
                'user_id' => $userid,
                'groupkey' => null,
            ]);

            $idplant = DB::table('plants')->where('name', $name)->value('id');
            $idsensors = DB::table('sensors')->pluck('id');

            foreach( $idsensors as $id){
                DB::table('sensor_plants')->insert([
                    ['plant_id' => $idplant, 'sensor_id' => $id],
                ]);
            }
    
            return response()->json([
                'msg' => 'peticion satisfactoria',
            ]);
        } catch (RequestException $error) {
            $statusCode = $error->getResponse() ? $error->getResponse()->getStatusCode() : 500;
            $responseData = $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null;
    
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $responseData,
            ], $statusCode);
        }
    }


    public function DeleteGroup(Request $request)
    {
        try {
            $name = $request->input('name');
            
            DB::table('plants')->where('name', $name)->delete();
    
            return response()->json([
                'msg' => 'peticion satisfactoria',
            ]);
        } catch (RequestException $error) {
            $statusCode = $error->getResponse() ? $error->getResponse()->getStatusCode() : 500;
            $responseData = $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null;
    
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $responseData,
            ], $statusCode);
        }
    }



        public function CreateFeed(Request $request)
    {
        try {
            $group = $request->input('Group');
            $name = $request->input('FeedName');
            $client = new Client();
            $response = $client->post('http://io.adafruit.com/api/v2/' . $this->username . '/groups/' . $group  . '/feeds', [
                'headers' => [
                    'X-AIO-Key' => $this->AIOKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $name,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            DB::table('sensors')->insert([
                'name' => $name
            ]);
            $idgroup = DB::table('plants')->where('groupkey', $group)->value('id');
            $idfeed = DB::table('sensors')->where('name', $name)->value('id');
            DB::table('sensor_plants')->insert([
                'plant_id' => $idgroup,
                'sensor_id' => $idfeed,
            ]);
    
            return response()->json([
                'msg' => 'peticion satisfactoria',
            ]);
        } catch (RequestException $error) {
            $statusCode = $error->getResponse() ? $error->getResponse()->getStatusCode() : 500;
            $responseData = $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null;
    
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $responseData,
            ], $statusCode);
        }
    }

    public function LastData(Request $request)
    {
        try {

            $feedskeys = DB::table('sensors')->pluck('feedkey');
            $datalist = [];
            
            foreach($feedskeys as $key){
                $client = new Client();
                $response = $client->get('https://io.adafruit.com/api/v2/' . $this->username . '/feeds/' . $key . '/data/last', [
                    'headers' => [
                        'X-AIO-Key' => $this->AIOKey,
                    ],
                ]);
                $data = json_decode($response->getBody(), true);

                $datalist[] = [
                    'feedkey' => $key,
                    'value' => $data['value'],
                ];

                if ($key == 'humedad' && $data['value'] >= 60){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'La humedad es mayor a 60%'],

                    );
                }
                if ($key == 'lluvia' && $data['value'] == 1){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'Esta lloviendo'],

                    );
                }
                if ($key == 'suelo' && $data['value'] <= 50){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'La humedad de la planta es menor a 50%'],

                    );
                }
                if ($key == 'temperatura' && $data['value'] >= 30){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'La temperatura es mayor a 30°'],

                    );
                }
                if ($key == 'agua' && $data['value'] <=20){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'Se esta acabando el agua'],

                    );
                }
                
                if ($key == 'luz' && $data['value'] < 20){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'Hay demasiada luz'],

                    );
                }
                if ($key == 'movimiento' && $data['value'] == 1){
                    $iduser = Auth()->user()->id;
                    DB::table('alerts')->insert(
                        ['user_id' => $iduser, 'message' => 'Hay movimiento cerca de tu planta!'],

                    );
                }
            }

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'data' => $datalist
            ]);

        } catch (RequestException $error) {
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null,
            ], 500);
        }
    }

    public function prueba()
    {
        $response = Http::withHeaders([
            'X-AIO-KEY'=>$this->AIOKey
        ])->post('http://io.adafruit.com/api/v2/'.$this->username.'/feeds/bomba/data?limit=1',["value"=>"1"]);
    
        if($response->ok()){
            sleep(5);
    
            $response = Http::withHeaders([
                'X-AIO-KEY'=>$this->AIOKey
            ])->post('http://io.adafruit.com/api/v2/'.$this->username.'/feeds/bomba/data?limit=1',["value"=>"0"]);
    
            if($response->ok()){
                return response()->json([
                    "msg"=>"La planta se ha regado y la bomba se ha apagado",
                    "data"=>$response->json()
                ],200);
            }else{
                return response()->json([
                    "msg"=>"Error al apagar la bomba",
                    "data"=>$response->body()
                ],$response->status());
            }
        }else{
            return response()->json([
                "msg"=>"Error en la peticion",
                "data"=>$response->body()
            ],$response->status());
        }
    }

    public function SendData(Request $request)
    {
        try {
            $Value = $request->input('Value');
            $Key = $request->input('FeedKey');
            $client = new Client();
            $response = $client->post('https://io.adafruit.com/api/v2/' . $this->username . '/feeds/' . $Key . '/data', [
                'headers' => [
                    'X-AIO-Key' => $this->AIOKey,
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => [
                    'value' => $Value,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'data' => $data,
            ]);
        } catch (RequestException $error) {
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null,
            ], 500);
        }
    }


    public function RequestPlant(){
        $userid = Auth()->user()->id;
        $plant=DB::table('plants')->where('user_id', $userid)->get();
        if($plant){
            return response()->json(['msg'=>"Plantas",'data'=>$plant],200);
        }
        return response()->json(['msg'=>"Plantas de usario no encontradas"],404);
    }

    public function alerts(){
        $userid = Auth()->user()->id;
        $alert = DB::table('alerts')->where('user_id',$userid)->orderBy('id', 'desc')->first();
    
        if($alert){
            return response()->json(['msg'=>"Alerta",'data'=>$alert],200);
        }
        return response()->json(['msg'=>"Alerta de usuario no encontrada"],404);
    }


    
}
