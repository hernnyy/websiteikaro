<?php
require "Slim/Slim.php";
require "notorm-master/NotORM.php";
use \Slim\Slim;

Slim::registerAutoloader();

// creamos una nueva instancia de Slim
$wsUserCommon = new Slim();
//carga de configuraciones
$array_ini = parse_ini_file("config.ini");

$pdo = new PDO($array_ini['dbname'],$array_ini['dbuser'], $array_ini['dbpass']);
$structure = new NotORM_Structure_Convention(
    $primary = "id_%s", // id_$table
    $foreign = "fk_id_%s", // id_$table
    $table = "%s", // {$table}s
    $prefix = "" // wp_$table
);
// $structure = new NotORM_Structure_Discovery($pdo, $cache = null, $foreign = '%s');
$db = new NotORM($pdo,$structure);

$wsUserCommon->post("/login", function () use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $data = $wsUserCommon->request()->post();
    $userToLogin = array(
        "active" => "1",
        "username" => $data["username"],
        "password" => $data["password"]
    );
    $users = $db->emt_users()->where($userToLogin);
    if ($user = $users->fetch()) {
        $jsonResponse = array(
            "id" => $user["id_emt_users"],
            "isValid" => true,
            "status" => true,
            "message" => "Usuario Valido"
            );
        foreach ($user->emt_persons() as $person) {
            $jsonResponse = array(
            "name" => $person["first_name"],
            "email" => $person->emt_contacts["email"],
            "id" => $user["id_emt_users"],
            "isValid" => true,
            "status" => true,
            "message" => "Usuario Valido"
            );
        }
        echo json_encode($jsonResponse);
    }else{
        echo json_encode(array(
            "status" => false,
            "data" => $data,
            "message" => "No existe un Usuario $username "
            ));
    }

});

$wsUserCommon->post("/insert", function () use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    //username=testpost&password=testa
    $user = $wsUserCommon->request()->post();
    $result = $db->emt_users()->insert($user);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id_emt_users"]));

});

$wsUserCommon->get("/delete/:id", function ($id) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    $unactivate = array(
        "active" => "0"
    );
    $user = $db->emt_users[$id];
 if ($user) {
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
    
    $user = $db->emt_users[$id];
    if ($user) {
        $jsonResponse = array(
            "id" => $user["id_emt_users"],
            "username" => $user["username"]
            );
        foreach ($user->emt_persons() as $person) {
            $jsonResponse = array(
            "name" => $person["first_name"],
            "email" => $person->emt_contacts["email"],
            "id" => $user["id_emt_users"],
            "username" => $user["username"]
            );
        }

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
    foreach ($db->emt_users()->where("active", "1") as $user) {
        $jsonResponse []  = array(
            "id" => $user["id_emt_users"],
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