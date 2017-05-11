<?php
require "Slim/Slim.php";
require "notorm-master/NotORM.php";
use \Slim\Slim;

Slim::registerAutoloader();

// creamos una nueva instancia de Slim
$wsUserCommon = new Slim();
//carga de configuraciones
$array_ini = parse_ini_file("config.ini");

$pdo = new PDO("mysql:dbname=u693453499_turno","u693453499_turno", $array_ini['dbpass']);
$db = new NotORM($pdo);

$wsUserCommon->get("/loginUser/:username/:password", function ($username,$password) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    //$user = $wsUserCommon->request()->post();
    $users = $db->users()->where("username", $username);
    //->where("password", $password)
    if ($user = $users->fetch()) {
        $jsonResponse []  = array(
            "id" => $user["id_user"],
            "username" => $user["username"],
            "firstname" => $user["firstname"],
            "lastname" => $user["lastname"],
            "birthday" => $user["birthday"],
            "email" => $user["email"],
            "telephone" => $user["telephone"],
            "identication_type" => $user["identication_type"],
            "identication_number" => $user["identication_number"],
            "state" => $user["state"],
            "activation_code" => $user["activation_code"]
        );
        echo json_encode(array(
            "data" => $user["email"],
            "isValid" => true,
            "status" => "ok",
            "message" => "Usuario Valido"
            ));
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un Usuario $username "
            ));
    }

});

$wsUserCommon->post("/insertUser", function (Request $request, Response $response) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    $user = $request->getParsedBody();
    // $user = $wsUserCommon->request()->post();
    echo json_encode($user);
    $result = $db->emt_users()->insert($user);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id"]));

});

$wsUserCommon->get("/deleteUser/:id", function ($id) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $users = $db->emt_users()->where("emt_user_id", $id);
    if ($users->fetch()) {
        $result = $users->delete();
        echo json_encode($result);

        echo json_encode(array(
            "status" => true,
            "message" => "Registro borrado exitosamente"
        ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "El Registro con id $id no existe"
        ));
    }

});
 
$wsUserCommon->get("/getByID/:id", function ($id) use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $users = $db->emt_users()->where("emt_user_id", $id);
    if ($user = $users->fetch()) {
        $jsonResponse []  = array(
            "id" => $user["id_user"],
            "username" => $user["username"]
        );

        echo json_encode($jsonResponse);
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

});

//consulta vacia
$wsUserCommon->get("/getAll", function () use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->emt_users() as $user) {
        $jsonResponse []  = array(
            "id" => $user["emt_user_id"],
            "username" => $user["username"]
            // "firstname" => $user["firstname"],
            // "lastname" => $user["lastname"],
            // "birthday" => $user["birthday"],
            // "email" => $user["email"],
            // "telephone" => $user["telephone"],
            // "identication_type" => $user["identication_type"],
            // "identication_number" => $user["identication_number"],
            // "state" => $user["state"],
            // "activation_code" => $user["activation_code"]
        );
    }

    echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsUserCommon->run();