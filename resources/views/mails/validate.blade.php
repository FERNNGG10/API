<!DOCTYPE html>
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
        <p>¡Gracias por unirte a nuestra comunidad! Haz clic en el siguiente enlace para activar tu cuenta:</p>
        <a class="btn" href="{{$signed_route}}">Activar cuenta</a>
       
    </div>
</body>
</html>