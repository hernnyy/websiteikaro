<?php
require "Slim/Slim.php";
require "notorm-master/NotORM.php";
include 'UtilMeet.php';
use \Slim\Slim;

Slim::registerAutoloader();

// creamos una nueva instancia de Slim
$wsMeetCommon = new Slim();
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

$wsMeetCommon->post("/insert", function () use ($wsMeetCommon, $db){    

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    //username=testpost&password=testa
    $meet = $wsMeetCommon->request()->post();
    $result = $db->emt_meets()->insert($meet);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id_emt_meets"]));

});

$wsMeetCommon->get("/delete/:id", function ($id) use ($wsMeetCommon, $db){    

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    $unactivate = array(
        "active" => "0"
    );
    $meet = $db->emt_meets[$id];
 if ($meet) {
        $result = $meet->update($unactivate);
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
 
$wsMeetCommon->get("/getByID/:id", function ($id) use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    
    $meet = $db->emt_meets[$id];
    if ($meet) {
    	$provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
    	$customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];

        echo json_encode(UtilMeet::parseMeetDTO($meet, $provider, $customer, $meetplace));
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

});

//consulta vacia
$wsMeetCommon->get("/getAll", function () use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->emt_meets()->where("active", "1") as $meet) {

        $provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
        $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];

        $jsonResponse []  = UtilMeet::parseMeetDTO($meet, $provider, $customer, $meetplace);
    }

    echo json_encode($jsonResponse);

});

$wsMeetCommon->get("/cancel/:id", function ($id) use ($wsMeetCommon, $db){    

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    $cancelQuery = array(
        "status" => "cancel"
    );
    $meet = $db->emt_meets[$id];
 if ($meet) {
        $result = $meet->update($cancelQuery);
        if($result !== false && $result !== 0){
            echo json_encode(array(
            "status" => true,
            "message" => "Registro Cancelado, con el id $id "
            ));
        }else{
            echo json_encode(array(
            "status" => false,
            "message" => "registro no cancelado o ya estaba cancelado"
            ));
        }
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

});

$wsMeetCommon->get("/getByCustomerID/:id", function ($id) use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    $clausulaCustomMeet = array(
        "active" => "0",
        "fk_id_emt_customers" => $id
    );
    $jsonResponse = array();
    foreach ($db->emt_meets()->where($clausulaCustomMeet) as $meet) {

        $provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
        $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];

        $jsonResponse []  = UtilMeet::parseMeetDTO($meet, $provider, $customer, $meetplace);
    }

    echo json_encode($jsonResponse);

});

// WS para traer todos los turnos de un usuario sea prov o cust
$wsMeetCommon->get("/getAllByUser/:id", function ($id) use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");

    $user = $db->emt_users[$id];
    $meetByUserC = array(
        "active" => "1",
        "fk_id_emt_customers" => $user->emt_customers["id_emt_customers"]
    );
    $meetByUserP = array(
        "active" => "1",
        "fk_id_emt_providers" => $user->emt_providers["id_emt_providers"]
    );

    $jsonCustomers = array();
    foreach ($db->emt_meets()->where($meetByUserC) as $meet) {
        $provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
        $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];

        $jsonCustomers []  = UtilMeet::parseMeetDTO($meet, $provider, $customer, $meetplace);
    }
    $jsonProviders = array();
    foreach ($db->emt_meets()->where($meetByUserP) as $meet) {
        $provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
        $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];

        $jsonProviders []  = UtilMeet::parseMeetDTO($meet, $provider, $customer, $meetplace);
    }
    $jsonResponse = array();
    $jsonResponse = array(
            "custom" => $jsonCustomers,
            "provider" => $jsonProviders
        );
    echo json_encode($jsonResponse);

});

// WS para traer todos los turnos asignados a un usuario prov, para ver su disponibilidad
$wsMeetCommon->post("/search", function () use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    //id=1&date=2017/11/01
    $params = $wsMeetCommon->request()->post();
    $user = $db->emt_users[$params["id"]];

    $jsonProviders = array();
    foreach ($db->emt_meets()->where("active = ? AND fk_id_emt_providers = ? AND DATE_FORMAT(date, '%Y-%m-%d') >= DATE_FORMAT(?, '%Y-%m-%d')", "1", $user->emt_providers["id_emt_providers"], $params["date"])->order("date DESC") as $meet) {
        $jsonProviders []  = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"]
        );
    }
    $jsonResponse = $jsonProviders;
    echo json_encode($jsonResponse);

});

// WS para traer todos los turnos asignados a un usuario customer, para ver sus turnos asignados
$wsMeetCommon->post("/search2", function () use ($wsMeetCommon, $db){

    $wsMeetCommon->response()->header("Content-Type", "application/json");
    //id=1&date=2017/11/01
    $params = $wsMeetCommon->request()->post();
    $user = $db->emt_users[$params["id"]];

    $jsonProviders = array();
    foreach ($db->emt_meets()->where("active = ? AND fk_id_emt_providers = ? AND DATE_FORMAT(date, '%Y-%m-%d') >= DATE_FORMAT(?, '%Y-%m-%d') AND DATE_FORMAT(date, '%Y-%m-%d') <= DATE_FORMAT(DATE_ADD(?, INTERVAL 3 DAY ), '%Y-%m-%d')", "1", $user->emt_providers["id_emt_providers"], $params["date"], $params["date"])->order("date DESC") as $meet) {
        $jsonProviders []  = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"]
        );
    }
    $jsonResponse = $jsonProviders;
    echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsMeetCommon->run();