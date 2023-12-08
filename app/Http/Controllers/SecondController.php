<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

            $userid = 8; //Auth()->user()->id;
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


    public function LastData(Request $request)
    {
        try {
            $Key = $request->input('FeedKey');
            $client = new Client();
            $response = $client->get('http://io.adafruit.com/api/v2/' . $this->username . '/feeds/' . $Key . '/data/last', [
                'headers' => [
                    'X-AIO-Key' => $this->AIOKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'data' => [
                    'feed_id' => $data['feed_id'],
                    'id' => $data['id'],
                    'value' => $data['value'],
                ],
            ]);
        } catch (RequestException $error) {
            return response()->json([
                'msg' => 'Error en la petición',
                'data' => $error->getResponse() ? json_decode($error->getResponse()->getBody(), true) : null,
            ], 500);
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















    public function CreateGroup(Request $request)
    {
        try {
            $name = $request->input('name');
            
            $client = new Client();
            $response = $client->post('http://io.adafruit.com/api/v2/' . $this->username . '/groups', [
                'headers' => [
                    'X-AIO-Key' => $this->AIOKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $name,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            $key = $data['key'];

            $userid = Auth()->user()->id;
            DB::table('plants')->insert([
                'name' => $name,
                'user_id' => $userid,
                'groupkey' => $key,
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

    public function prueba()
    {
        $response = Http::withHeaders([
            'X-AIO-KEY'=>$this->AIOKey
        ])->get('http://io.adafruit.com/api/v2/'.$this->username.'/feeds/luz/data?limit=1');

        if($response->ok()){
            return response()->json([
                "msg"=>"si jala",
                "data"=>$response->json()
            ],200);
        }else{
            return response()->json([
                "msg"=>"Error en la peticion",
                "data"=>$response->body()
            ],$response->status());
        }
    }
    
}
