<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nette\Utils\Json;
use App\Http\Controllers\SecondController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);
    Route::post('register',[AuthController::class,'register']);
});


Route::get('/apitest', function () {
    return response()->json(["hola"],200);
});


Route::prefix('group')->group(function () {
    Route::get('/all/group', [SecondController::class, 'AllGroup']);
    Route::post('/one/group', [SecondController::class, 'GroupFeed']);
    Route::post('/last/data', [SecondController::class, 'LastData']);
    Route::post('/create/group', [SecondController::class, 'CreateGroup']);
    Route::post('/create/group/feed', [SecondController::class, 'CreateFeed']);
});


