<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SecondController extends Controller
{
    private const AIOKey = 'aio_NGko40ADvFYplxlMUOeiQiWOdRPE';
    private const username = 'Arthur65';

    public function AllGroup(Request $request)
    {
        try {
            $client = new Client();
            $response = $client->get('http://io.adafruit.com/api/v2/' . self::username . '/groups', [
                'headers' => [
                    'X-AIO-Key' => self::AIOKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);

            return response()->json([
                'msg' => 'peticion satisfactoria',
                'IDowner' => collect($data)->pluck('owner.id')->unique()->values()->all(),
                'owner' => collect($data)->pluck('owner.username')->unique()->values()->all(),
                'groups' => collect($data)->map(function ($item) {
                    return [
                        'idgroup' => $item['id'],
                        'group' => $item['name'],
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


    public function GroupFeed(Request $request)
    {
        try {
            $Key = $request->input('GroupKey');
            $client = new Client();
            $response = $client->get('http://io.adafruit.com/api/v2/' . self::username . '/groups/'. $Key, [
                'headers' => [
                    'X-AIO-Key' => self::AIOKey,
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
            $response = $client->get('http://io.adafruit.com/api/v2/' . self::username . '/feeds/' . $Key . '/data/last', [
                'headers' => [
                    'X-AIO-Key' => self::AIOKey,
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














    public function CreateGroup(Request $request)
    {
        try {
            $name = $request->input('name');
            
            $client = new Client();
            $response = $client->post('http://io.adafruit.com/api/v2/' . self::username . '/groups', [
                'headers' => [
                    'X-AIO-Key' => self::AIOKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $name,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
    
            return response()->json([
                'msg' => 'peticion satisfactoria',
                'data' => $data,
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
            $response = $client->post('http://io.adafruit.com/api/v2/' . self::username . '/groups/' . $group  . '/feeds', [
                'headers' => [
                    'X-AIO-Key' => self::AIOKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $name,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
    
            return response()->json([
                'msg' => 'peticion satisfactoria',
                'data' => $data,
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
    
}
