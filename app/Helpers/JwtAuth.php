<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {
    public $key;
    public function __construct() {
        $this->key='ADSJLK2J342IJD2JD928UR28FHCPQFJOHWOER9HR942HF2HF94R92HFWHEFOHRU3P49HR39PHFP4H39493493T0352U0324UT3GEKJRTJ9U33845034857038576354TGPEOGJBPOIEH';
    }

    public function singup($email, $password, $getToken = null) {
        $usuario = User::where(array(
                    'email' => $email,
                    'password' => $password
                ))->first();
        $sigup = false;
        if (is_object($usuario)) {
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
            $jwt =JWT::decode($token, $this->key,'HS256');
            $decode=    JWT::decode($jwt, $this->key,array('HS256'));
            if (!is_null($getToken)) {
                
                return $jwt;
            }else if ($getToken==false || $getToken==null) {
                return $decode;
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
        }catch(\DomainException $ee)
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
