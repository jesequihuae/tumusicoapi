<?php 

    require 'vendor/autoload.php';
    require 'conection.php';
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    $c = new \Slim\Container();
    $c['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
        	$error = array('error' => $exception->getMessage());
          return $c['response']->withStatus(500)
                                 ->withHeader('Content-Type', 'application/json')
                                 ->write(json_encode($error));
        };
    };

    $app = new \Slim\App($c);

    require 'utils.php';

    $app->get('/',function(Request $request, Response $response, $args){
        echo 'hola mundo';
    });

    $app->get('/pruebaPython/{camara}/{tipo}',function(Request $request, Response $response, $args){
        $alerta = new Alertas();
        $alerta->camara = $args['camara'];
        $alerta->tipo = $args['tipo'];
        $alerta->fecha = date("F j, Y, g:i a");
        $alerta->save();
        echo "alerta guardada";
    });

    $app->get('/obtenerDatos',function(Request $request, Response $response, $args){
        $alerta = Alertas::get();
        return sendOkResponse($alerta->toJson(),$response);
    });

    /**** Músico ****/
    $app->group('/musico',function(){
        /* Obtener todos los músicos registrados */
        $this->get('/all',function(Request $request, Response $response, $args){
            $musico = Musico::orderByRaw('RAND()')->get();
            return sendOkResponse($musico->toJson(),$response);
        });

        $this->get('/byCity/{idCiudad}', function(Request $request, Response $response, $args){
            $musico = Musico::where('c_ciudad_id','=',$args['idCiudad'])->orderByRaw('RAND()')->get();
            return sendOkResponse($musico->toJson(),$response);
        });

        /* Obtener músico individual */
        $this->get('/{id}',function(Request $request, Response $response, $args){
            $musico = Musico::where('id','=', $args['id'])->with('instrumentos','excepciones','trajes','horarios')->get();
            return sendOkResponse($musico->toJson(), $response);
        });

        /* Registrar un nuevo músico */
        $this->post('/new',function(Request $request, Response $response, $args){
            $data = $request->getParsedBody();
            $Musico = new Musico();


            //Nombre imagen
            $nombreImagen = $data['nombre'].$data['apellido_paterno'].uniqid();    
            $decoded = base64_decode($data['foto_perfil']);
            file_put_contents('imgPerfil/'.$nombreImagen.'.jpg', $decoded);

            $Musico->nombre = $data['nombre'];
            $Musico->apellido_paterno = $data['apellido_paterno'];
            $Musico->apellido_materno = $data['apellido_materno'];
            $Musico->telefono1 = $data['telefono1'];
            $Musico->telefono2 = $data['telefono2'];
            $Musico->disponibilidad = $data['disponibilidad'];
            #Crear carpeta en servidor para subir la imagen de perfil
            $Musico->foto_perfil = 'imgPerfil/'.$nombreImagen.'.jpg';
            $Musico->usuario = $data['usuario'];
            $Musico->password = $data['password'];
            $Musico->fecha_registro = $data['fecha_registro'];
            $Musico->destacado = $data['destacado'];
            $Musico->fecha_destacado = $data['fecha_destacado'];
            $Musico->c_pais_id = "1";//$data['c_pais_id']; TODOS SON DE MEXICO
            $Musico->c_estado_id = $data['c_estado_id'];
            $Musico->c_ciudad_id = $data['c_ciudad_id'];
            $Musico->save();

            $Ultimo = Musico::all();

            // return $response->getBody()->write($data->nombre);
            return sendOkResponse($Ultimo->last()->toJson(),$response);
        });
    });

    /**** Países ****/
    $app->group('/ubicacion',function(){
        /* Obtener los países */
        $this->get('/paises',function(Request $request, Response $response, $args){
            $paises = Pais::get();
            return sendOkResponse($paises->toJson(),$response);
        });

        /* Obtener los estados*/
        $this->get('/estados/{pais}',function(Request $request, Response $response, $args){
            $estados = Estado::where('c_pais_id','=', $args['pais'])->get();
            return sendOkResponse($estados->toJson(),$response);
        });
        /* Obtener las ciudades */
        $this->get('/ciudades/{estado}',function(Request $request, Response $response, $args){
            $ciudades = Ciudad::where('c_estado_id','=',$args['estado'])->get();
            return sendOkResponse($ciudades->toJson(),$response);
        });
    });
    
    $app->run();

?>