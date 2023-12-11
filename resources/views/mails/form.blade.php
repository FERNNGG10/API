<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Life Plants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body >
   
  <div class="container text-center">
    <div class="row align-items-start">
        <div class="col"></div>
        <div class="col border border-dark-subtle mt-5 bg-light">
         
            <form class="" method="post" action="{{ route('resetpassword', ['email' => $email]) }}">
                @csrf
                <label for="exampleInputEmail1" class="form-label">Contraseña</label>

                <input type="password" class="form-control " name="password" value="{{old('password')}}">

                <div id="emailHelp" class="form-text">Nunca compartiremos su contraseña con nadie más.</div>

              
                <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" value="{{old('password_confirmation')}}">
                </div>
                <button type="submit" class="btn btn-success" value="Enviar">Submit</button>
                @if(Session::has('error'))
                    <div class="alert alert-danger text-center" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @elseif(Session::has('success'))
                    <div class="alert alert-success text-center" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif
            </form>
        </div>
        <div class="col">
          @foreach($errors->all() as $error)
              <p>{{$error}}</p>
          @endforeach
        </div>
    </div>
  </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  </body>
</html>