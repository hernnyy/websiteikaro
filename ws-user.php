<?php
require "Slim/Slim.php";
require "notorm-master/NotORM.php";
include 'UtilUser.php';
include 'UtilMeetPlace.php';
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
        echo json_encode(UtilUser::parseUserDTO($user));
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
        echo json_encode(UtilUser::parseUserDTO($user));
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
        $jsonResponse [] = UtilUser::parseUserDTO($user);
    }

    echo json_encode($jsonResponse);

});

$wsUserCommon->get("/getAllPlaces", function () use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->emt_meetplaces()->where("active", "1") as $meetplace) {
        $jsonResponse []  = UtilMeetPlace::parsePlaceDTO($meetplace);
    }

    echo json_encode($jsonResponse);

});

$wsUserCommon->get("/getAllCust/:id", function ($id) use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->emt_users()->where("active = ? AND fk_id_emt_customers <> ? AND fk_id_emt_customers IS NOT NULL", "1", $id) as $user) {
        $jsonResponse [] = UtilUser::parseUserDTO($user);
    }

    echo json_encode($jsonResponse);

});

$wsUserCommon->get("/getAllProv/:id", function ($id) use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->emt_users()->where("active = ? AND fk_id_emt_providers <> ? AND fk_id_emt_providers IS NOT NULL", "1", $id) as $user) {
        $jsonResponse [] = UtilUser::parseUserDTO($user);
    }

    echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsUserCommon->run();