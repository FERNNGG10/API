<?php

namespace App\Http\Controllers;

use App\Mail\ValidatorEmail;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
     /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','activate','resend_email','forget_password','forget','resetpassword']]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        
        $credentials = request(['email', 'password']);
        
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $count = DB::table('plants')->where('user_id', auth()->user()->id)->count();
        $user = auth()->user()->toArray();
        $user['plants'] = $count;
        return response()->json($user, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()*24*512 //minutos entre 60min entre 24 horas ej: 43200/60/24
        ]);
    }

    public function register (Request $request){

        //validacion de datos
        $validate = Validator::make(
            $request->all(),[
                "name"  =>  "required|max:30",
                "email" =>  "required|unique:users|email",
                "password"  =>  "required|min:8|string|confirmed"
            ],  [
                "name.required" => "El nombre es obligatorio.",
                "name.max" => "El nombre no puede tener más de :max caracteres.",
                "email.required" => "El correo electrónico es obligatorio.",
                "email.unique" => "Este correo electrónico ya está registrado.",
                "email.email" => "Por favor, introduce un correo electrónico válido.",
                "password.required" => "La contraseña es obligatoria.",
                "password.min" => "La contraseña debe tener al menos :min caracteres.",
                "password.string" => "La contraseña debe ser una cadena de caracteres.",
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            ]
            );
            if($validate->fails()){
             
                return response()->json(["msg"=>"Error en datos","data"=>$validate->errors()],422);
            }
            //creacion de usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            //ruta signada o temporal para activacion de cuenta
            $signed_route = URL::temporarySignedRoute(
                'activate',
                now()->addMinutes(15),
                ['user' =>  $user->id]
            );
            Mail::to($request->email)->send(new ValidatorEmail($signed_route));
            return response()->json(["msg"=>"Se mando un mensaje a tu correo","data"=>$user],201);
    }

    public function activate (User $user){
        $user->is_active=true;
        $user->save();
        return '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #333;
                    text-align: center;
                }
                p {
                    color: #666;
                    line-height: 1.6;
                }
                .btn {
                    display: block;
                    width: 200px;
                    margin: 20px auto;
                    padding: 10px 15px;
                    text-align: center;
                    color: #fff;
                    background-color: #3AC307;
                    border: none;
                    border-radius: 5px;
                    text-decoration: none;
                    
                }
             
            </style>
            <title>EMAIL DE ACTIVACIÓN</title>
        </head>
        <body>
            <div class="container">
                <h1>¡Bienvenido a LifePlant!</h1>
                <p class="btn">¡Gracias por unirte a nuestra comunidad! Regresa a la app para iniciar sesion :)</p>
               
            </div>
        </body>
        </html>';
    }

    public function resend_email(Request $request){
        $request->all();
        $user = User::where('email',$request->email)->first();
        if($user){
            $signed_route = URL::temporarySignedRoute(
                'activate',
                now()->addMinutes(15),
                ['user' =>  $user->id]
            );
            Mail::to($request->email)->send(new ValidatorEmail($signed_route));
            return response()->json(["msg"=>"Se reenvio un mensaje a tu correo","data"=>$user->email],201);
        
        }
        else{
            return response()->json(["msg"=>"No se encontro el correo","data"=>$user],404);
        }
    }

    public function change_password(Request $request){
        
        $user_id = auth()->user()->id;
        $validate = Validator::make(
            $request->all(),[
                "password"  =>  "required|min:8|string|confirmed"
            ],[
                "password.required" => "La contraseña es obligatoria.",
                "password.min" => "La contraseña debe tener al menos :min caracteres.",
                "password.string" => "La contraseña debe ser una cadena de caracteres.",
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
                ]
            );

        if($validate->fails()){
            return response()->json([
                "msg"   =>"Error en la validacion de datos",
                "data"  =>$validate->errors()
            ],422);
        }else{
            $user = User::find($user_id);
            if($user){
                $user->password=Hash::make($request->password);
                $user->save();
                return response()->json(["msg"=>"Contraseña cambiada correctamente","data"=>$user],200);
            }
            return response()->json([
                "msg"   =>"Usuario no encotrado"
            ],404);
        }
    }
    //post
    public function forget_password(Request $request){
        $validate = Validator::make(
            $request->all(),[
                "email" =>  "required|email"
            ],
            [
                "email.required" => "El correo electrónico es obligatorio.",
                "email.email" => "Por favor, introduce un correo electrónico válido."
            ]
            );

            if($validate->fails()){
                return response()->json([
                    "msg"   =>"Error en la validacion de datos",
                    "data"  =>$validate->errors()
                ],422);
            }

            $signed_route = URL::temporarySignedRoute(
                'forget',
                now()->addMinutes(15),
                ['email' =>  $request->email]
            );
            Mail::to($request->email)->send(new ForgetPasswordMail($signed_route));
            return response()->json(['msg'=>'Se envio un correo a '.$request->email]);
    }
    
    //get
    public function forget($email){
        return response()->view('mails.form', ['email' => $email, 'errors' => new MessageBag()]);
    }

    //put
    public function resetpassword(Request $request, $email){
        $request->validate([
            "password"  =>  "required|min:8|string|confirmed"
        ],[
            "password.required" => "La contraseña es obligatoria.",
            "password.min" => "La contraseña debe tener al menos :min caracteres.",
            "password.string" => "La contraseña debe ser una cadena de caracteres."
        ]);
    
        $user = User::where('email',$email)->first();
        if($user){
            $user->password=Hash::make($request->password);
            $user->save();
            return '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        padding: 20px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        color: #333;
                        text-align: center;
                    }
                    p {
                        color: #666;
                        line-height: 1.6;
                    }
                    .btn {
                        display: block;
                        width: 200px;
                        margin: 20px auto;
                        padding: 10px 15px;
                        text-align: center;
                        color: #fff;
                        background-color: #3AC307;
                        border: none;
                        border-radius: 5px;
                        text-decoration: none;
                        
                    }
                 
                </style>
                <title>EMAIL DE ACTIVACIÓN</title>
            </head>
            <body>
                <div class="container">
                    <h1>¡LifePlant!</h1>
                    <p class="btn">¡Tu contraseña a sido cambiada exitosamente, puede regresar a la aplicación:)</p>
                   
                </div>
            </body>
            </html>';
        }
        return redirect()->back()->withErrors(['error' => 'Usuario no encontrado']);
    }


}
