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
        $car = Car::find($id);
        if (!is_null($car)) {
            $car = Car::find($id)->load('user');
            $data = array(
                'car'=>$car,
                'codigo' => 200,
                'estado' => 'procesado',
                'Mensaje' => 'Se encontraron registros'
            );
        } else {
            $data = array(
                'codigo' => 400,
                'estado' => 'Error!',
                'Mensaje' => 'No exixste ningun registro con este ID: '.$id
            );
        }
        return response()->json($data, 200);
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

            //Validando datos que ingresan por el Request
            $validacion = \Validator::make($parametroValida, [
                        'title' => 'required',
                        'description' => 'required',
                        'price' => 'required',
                        'status' => 'required'
            ]);
            //Llenamos el modelo con los datos que llegaron en la request
            $car = Car::where('id', $id)->update($parametroValida);
            $data = array(
                'car' => $parametros,
                'codigo' => 400,
                'estado' => 'success',
                'mensaje' => 'Se actualizo de forma correcta'
            );
        } else {
            $data = array(
                'codigo' => 400,
                'estado' => 'fail',
                'mensaje' => 'Error en la autentificacion del usuario'
            );
        }
        return response()->json($data, 200);
    }

    public function destroy(Request $resultado, $id) {
        $hash = $resultado->header('Autorizacion', null);
        $jwt = new JwtAuth();
        $validacionUsuario = $jwt->checkToken($hash);

        if ($validacionUsuario == true) {
            $car = Car::find($id);
            if (!is_null($car)) {
                $car = Car::where('id', $id)->delete();
                $data = array(
                    'codigo' => 200,
                    'estado' => 'procesado',
                    'mensaje' => 'Se elimino de forma correcta el coche'
                );
            } else {
                $data = array(
                    'codigo' => 400,
                    'estado' => 'Error',
                    'mensaje' => 'Este registro no existe'
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'estado' => 'error',
                'mensaje' => 'Error en la autentificacion'
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
