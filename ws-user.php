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
        $jsonResponse []  = UtilUser::parsePlaceDTO($meetplace);
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

class UtilUser
{
    // Declaración de una propiedad
    public $var = 'un valor predeterminado';

    public static function parseUserDTO($user) {
        foreach ($user->emt_persons() as $person) {
            $jsonPerson = array(
            "first_name" => $person["first_name"],
            "last_name" => $person["last_name"],
            "document_number" => $person["document_number"],
            "document_type" => $person["document_type"],
            "contact" => array(
                "id" => $person->emt_contacts["id_emt_contacts"],
                "email" => $person->emt_contacts["email"],
                "email2" => $person->emt_contacts["email2"],
                "cellphone" => $person->emt_contacts["cellphone"],
                "cellphone2" => $person->emt_contacts["cellphone2"]
                )
            );
        }
        $jsonResponse = array(
            "id" => $user["id_emt_users"],
            "username" => $user["username"],
            "customer" => array(
                "id" => $user->emt_customers["id_emt_customers"],
                "dots" => $user->emt_customers["dots"]
                ),
            "provider" => array(
                "id" => $user->emt_providers["id_emt_providers"],
                "dots" => $user->emt_providers["dots"]
                ),
            "person" => $jsonPerson,
            "isValid" => true,
            "status" => true,
            "message" => "User OK"
            );
        return $jsonResponse;
    }

    public static function parsePlaceDTO($place) {
        foreach ($place->emt_contacts() as $contact) {
            $jsonContact = array(
                "id" => $contact["id_emt_contacts"],
                "email" => $contact["email"],
                "email2" => $contact["email2"],
                "cellphone" => $contact["cellphone"],
                "cellphone2" => $contact["cellphone2"]
            );
        }
        $jsonResponse = array(
            "id" => $place["id_emt_meetplaces"],
            "fantasy_name" => $place["fantasy_name"],
            "status" => $place["status"],
            "name" => $place["name"],
            "addres" => array(
                "id" => $place->emt_address["id_emt_address"],
                "country_code" => $place->emt_address["country_code"],
                "country" => $place->emt_address["country"],
                "street_name" => $place->emt_address["street_name"],
                "street_number" => $place->emt_address["street_number"],
                "postal_code" => $place->emt_address["postal_code"],
                "locality" => $place->emt_address["locality"],
                "region" => $place->emt_address["region"]
                ),
            "contact" => $jsonContact,
            "isValid" => true,
            "status" => true,
            "message" => "Place OK"
            );
        return $jsonResponse;
    }
}