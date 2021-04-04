<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de pruebas de USER-CONTROLLER";
    }

    public function register(Request  $request){

        //Recoger los datos del usuario por post
        $json = $request->input('json', null);
        $params = json_decode($json); //Objeto
        $params_array = json_decode($json, true); // array

        //Los datos no estan vacios 
        if(!empty($params) && !empty($params_array)){
            //Limpiar datos
            $params_array = array_map('trim', $params_array);

            //Validar los datos
            $validate = \Validator::make($params_array, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users',// comprobar si el usuario esta duplicado
                'password'  => 'required'
            ]);

            if($validate->fails()){
                // La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code'   => '404',
                    'message'=> 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            }else{//Validacion correcta

                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                $user->save();

                $data = array(
                    'status' => 'success',
                    'code'   => '200',
                    'message'=> 'El usuario se ha creado correctamente',
                    'user'   => $user
                );
            }
        }else{// Datos vacios o nulos
            $data = array(
                'status' => 'error',
                'code'   => '404',
                'message'=> 'Los datos enviados no son correctos'
            );
        }
        
        return response()->json($data, $data['code']);
    }

    public function login(Request  $request){

        $jwtAuth = new \JwtAuth();

        //Recibir los datos
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate = \Validator::make($params_array, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if($validate->fails()){
            // La validacion ha fallado
            $signup = array(
                'status' => 'error',
                'code'   => '404',
                'message'=> 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        }else{
            //cifrar contraseña
            $pwd = hash('sha256', $params->password);

            //devolver datos
            $signup = $jwtAuth->signup($params->email, $pwd);
        }

       
        
        return response()->json($signup, 200);
    }

    public function update(Request $request){

        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //Recoger los datos por post 
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if($checkToken && !empty($params_array)){
           
            
            
            //Sacar usuario identificado 
            $user = $jwtAuth->checkToken($token, true);

            //Validar datos 
            $validate = \Validator::make($params_array,[
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'requires|email|unique:users,'.$user->sub
            ]);

            //Quitar los datos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            //actualizar usuario bbdd
            $user_update = User::where('id', $user->sub)->update($params_array);

            //Devolver array con resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array
            );
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no ésta identificado'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request){
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no ésta identificado'
        );

        return response($data, $data['code'])->header('Content-Type', 'text/plain');
    }
}
