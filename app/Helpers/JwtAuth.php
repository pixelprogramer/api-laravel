<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {
    public $key;
    public function __construct() {
        $this->key='p-i-x-e-l-p-r-o-g-r-a-m-e-r-c-l-a-v-e-AJDIJ*24KSDA-WIUJOEN+X@QOJEOR$/%/IJWAFBWBV-EIR2R2398*-/5283YFJJDGJEJFQ-23J40-OIUU-8923KJECBKW-JEBRI+BI23UH4ID-QWWQEQECKJE*RJR-PIXELPROGRAMER-NELSON-ANDRES-GUERRERO-MONCADA';
    }

    public function singup($email, $password, $getToken = null) {
        $usuario = User::where(array(
                    'email' => $email,
                    'password' => $password
                ))->first();
        $sigup = false;
        if (!is_null($usuario)) {
            $sigup = true;
        }
        if ($sigup) {
            //Generar token
            $token = array(
            'sub' => $usuario->id,
            'email' => $usuario->name,
            'password' => $usuario->password,
            'iat' => time(),
            'exp' => time() + (7*24*60*60),
            );
            $jwt =JWT::encode($token, $this->key,'HS256');
            $decode=    JWT::decode($jwt, $this->key,array('HS256'));
            if ($getToken==true) {
                return $decode;
            }else if ($getToken==false || $getToken==null) {
                return $jwt;
            }
        } else {
            return array(
                'status' => 'error',
                'code' => '400',
                'mensaje' => 'El ingreso a fallado'
            );
        }
    }
    
    public function checkToken($jwt,$getIdentity = false)
    {
        $auth=false;
        try {
            $decode= JWT::decode($jwt, $this->key,array('HS256'));
        } catch (\UnexpectedValueException $e) {
           $auth=false;
        }catch(\DomainException $e)
        {
            $auth =false;
        }
        
        if (is_object($decode) && isset($decode->sub)) {
            $auth=true;
        }else
        {
            $auth=false;
        }
        if ($getIdentity) {
            return $decode;
        }



        return $auth;
    }

}
