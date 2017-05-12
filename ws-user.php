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
$structure = new NotORM_Structure_Convention(
    $primary = "emt_user_id", // id_$table
    $foreign = "%s", // id_$table
    $table = "%s", // {$table}s
    $prefix = "emt_" // wp_$table
);
$db = new NotORM($pdo,$structure);

$wsUserCommon->post("/loginUser", function () use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $data = $wsUserCommon->request()->post();
    $userToLogin = array(
        "active" => "1",
        "username" => $data["username"],
        "password" => $data["password"]
    );
    $users = $db->users()->where($userToLogin);
    if ($user = $users->fetch()) {
        // $jsonResponse []  = array(
        //     "id" => $user["id_user"],
        //     "username" => $user["username"],
        //     "firstname" => $user["firstname"],
        //     "lastname" => $user["lastname"],
        //     "birthday" => $user["birthday"],
        //     "email" => $user["email"],
        //     "telephone" => $user["telephone"],
        //     "identication_type" => $user["identication_type"],
        //     "identication_number" => $user["identication_number"],
        //     "state" => $user["state"],
        //     "activation_code" => $user["activation_code"]
        // );
        echo json_encode(array(
            "data" => $user["email"],
            "isValid" => true,
            "status" => true,
            "message" => "Usuario Valido"
            ));
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un Usuario $username "
            ));
    }

});

$wsUserCommon->post("/insertUser", function () use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    //username=testpost&password=testa
    $user = $wsUserCommon->request()->post();
    $result = $db->emt_users()->insert($user);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id"]));

});

$wsUserCommon->get("/deleteUser/:id", function ($id) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    $unactivate = array(
        "active" => "0"
    );
    $user = $db->users()->where("emt_user_id", $id)->fetch();
 if ($user !== false) {
        $result = $user->update($unactivate);
        if($result !== false && $result !== 0){
            echo json_encode(array(
            "status" => true,
            "message" => "Registro Borrado, con el id $id "
            ));
        }else{
            echo json_encode(array(
            "status" => false,
            "message" => "registro no borrado o ya estaba borrado"
            ));
        }
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

});
 
$wsUserCommon->get("/getByID/:id", function ($id) use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $users = $db->users()->where("emt_user_id", $id);
    if ($user = $users->fetch()) {
        $jsonResponse []  = array(
            "id" => $user["emt_user_id"],
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
    foreach ($db->users()->where("active", "1") as $user) {
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