<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller {

    public function index(Request $resultado) {
        $hash = $resultado->header('Autorizacion', null);
        $jwt = new JwtAuth();
        $chechk = $jwt->checkToken($hash);
        if ($chechk == true) {
            $car = Car::all()->load('User');
            return response()->json(array(
                        'cars' => $car,
                            ), 200);
        } else {

            echo 'Error en la autentificacion';
        }
    }

    public function show($id) {
        $car = Car::find($id)->load('user');
        return response()->json(Array(
                    'car' => $car
                        ), 200);
    }

    function update(Request $resultado, $id) {
        $hash = $resultado->header('Autorizacion', null);
        $jwt = new JwtAuth();
        $validarUsuario = $jwt->checkToken($hash);

        if ($validarUsuario == true) {
            // Recojemos la recues y la convertimos a json
            $json = $resultado->input('json', null);
            $parametros = json_decode($json);
            $parametroValida = json_decode($json, true);
            $car = new Car();
            //Validando datos que ingresan por el Request
            $validacion = \Validator::make($parametroValida, [
                        'title' => 'required',
                        'description' => 'required',
                        'price' => 'required',
                        'status' => 'required'
            ]);
            //Llenamos el modelo con los datos que llegaron en la request
            $car->title=$parametros->title;
            $car->description = $parametros->description;
            $car->price=$parametros->price;
            $car->status=$parametros->status;
        } else {
            $data = array(
                'codigo' => 400,
                'estado' => 'fail',
                'mensaje' => 'Error en la autentificacion del usuario'
            );
        }
        return response()->json($data, 200);
    }

    public function store(Request $resultado) {
        $codigoAutentificacion = $resultado->header('Autorizacion', null);
        $jwt = new JwtAuth();
        $comprobanteAutorizacion = $jwt->checkToken($codigoAutentificacion);

        if ($comprobanteAutorizacion == true) {
            //Resivir post 
            $json = $resultado->input('json', null);
            $parametros = json_decode($json);
            $parametros_array = json_decode($json, true);
            //Validacion de datos
            $validacion = \Validator::make($parametros_array, [
                        'title' => 'required',
                        'description' => 'required',
                        'price' => 'required',
                        'status' => 'required'
            ]);

            if ($validacion->fails()) {
                return response()->json($validacion->errors(), 400);
            }
            //Guardar coche DB
            $user = $jwt->checkToken($codigoAutentificacion, true);
            $car = new Car();
            $car->user_id_fk = $user->sub;
            $car->title = $parametros->title;
            $car->description = $parametros->description;
            $car->price = $parametros->price;
            $car->status = $parametros->status;
            $car->save();
            $datos = array(
                'car' => $car,
                'codigo' => '200',
                'mensaje' => 'Coche creado de forma correcta'
            );
        } else {
            echo 'Error en la autentificacion';
        }

        return response()->json($datos, 200);
    }

}
