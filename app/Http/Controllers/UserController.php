<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\JwtAuth;

class UserController extends Controller {

    public function register(Request $resultado) {
        $json = $resultado->input('json', null);
        $parametros = json_decode($json);
        $email = (!is_null($json) && isset($parametros->email)) ? $parametros->email : null;
        $name = (!is_null($json) && isset($parametros->name)) ? $parametros->name : null;
        $password = (!is_null($json) && isset($parametros->password)) ? $parametros->password : null;
        $role = 'ROLE_USER';

        if (!is_null($email) && !is_null($name) && !is_null($password)) {
            $usuario = new User;
            $usuario->name = $name;
            $usuario->email = $email;
            $usuario->role = $role;
            $pwd = hash('sha256', $password);
            $usuario->password = $pwd;

            $iseet_user = User::where('email', '=', $email)->first();
            if (count($iseet_user) <= 0) {
                $usuario->save();
                $data = array(
                    'mensaje' => 'Usuario creado de forma correcta',
                    'status' => 'success',
                    'code' => '200',

                );
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'mensaje' => 'Ya existe un usuario con este correo'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'mensaje' => 'Usuario no creado'
            );
        }
        return response()->json($data, 200);
    }

    public function login(Request $resultado) {
        $jwt = new JwtAuth();
        $json = $resultado->input('json', null);
        $parametros = json_decode($json);
        $email = (!is_null($json) && isset($parametros->email)) ? $parametros->email : null;
        $password = (!is_null($json) && isset($parametros->password)) ? $parametros->password : null;
        $getToken = (!is_null($json) && isset($parametros->gettoken)) ? $parametros->gettoken : null;
        
        //Cifrar password-
        $pwd = hash('sha256', $password);
        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
            $signup=$jwt->singup($email, $pwd);
        }else if ($getToken!=null) {
            $signup=$jwt->singup($email, $pwd, $getToken);         
        }else
        {
            $signup=array(
                'estus'=>'Error',
                'Envia tus datos por post'
            );
        }
        return response()->json($signup,200);
    }

}
