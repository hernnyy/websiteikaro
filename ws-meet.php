<?php
require "Slim/Slim.php";
require "notorm-master/NotORM.php";
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

// $wsMeetCommon->post("/login", function () use ($wsMeetCommon, $db){    

//     $wsMeetCommon->response()->header("Content-Type", "application/json");
    
//     $data = $wsMeetCommon->request()->post();
//     $userToLogin = array(
//         "active" => "1",
//         "username" => $data["username"],
//         "password" => $data["password"]
//     );
//     $users = $db->emt_users()->where($userToLogin);
//     if ($user = $users->fetch()) {
//         $jsonResponse = array(
//             "id" => $user["id_emt_users"],
//             "isValid" => true,
//             "status" => true,
//             "message" => "Usuario Valido"
//             );
//         foreach ($user->emt_persons() as $person) {
//             $jsonResponse = array(
//             "name" => $person["first_name"],
//             "email" => $person->emt_contacts["email"],
//             "id" => $user["id_emt_users"],
//             "isValid" => true,
//             "status" => true,
//             "message" => "Usuario Valido"
//             );
//         }
//         echo json_encode($jsonResponse);
//     }else{
//         echo json_encode(array(
//             "status" => false,
//             "message" => "No existe un Usuario $username "
//             ));
//     }

// });

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
        foreach ($provider->emt_users() as $prov) {
            $provname = $prov["username"];
        }

    	$customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        foreach ($customer->emt_users() as $cust) {
            $custname = $cust["username"];
        }
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];
        $codepais = $meetplace->emt_address["country_code"];
        
        $jsonResponse = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"],
            "proveedor" => $meet->emt_providers["id_emt_providers"],
            "provname" => $provname,
            "custname" => $custname,
            "lugar" => $meet->emt_meetplaces["fantasy_name"],
            "codigoPais" => $codepais
            );
        // foreach ($meet->emt_persons() as $person) {
        //     $jsonResponse = array(
        //     "name" => $person["first_name"],
        //     "email" => $person->emt_contacts["email"],
        //     "id" => $meet["id_emt_users"],
        //     "username" => $meet["username"]
        //     );
        // }

        echo json_encode($jsonResponse);
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
        $jsonResponse []  = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"]
        );
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
    $meet = $db->emt_meets()->where($clausulaCustomMeet);
    if ($meet) {
        $provider = $db->emt_providers[$meet->emt_providers["id_emt_providers"]];
        foreach ($provider->emt_users() as $prov) {
            $provname = $prov["username"];
        }

        $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
        foreach ($customer->emt_users() as $cust) {
            $custname = $cust["username"];
        }
        $meetplace = $db->emt_meetplaces[$meet->emt_meetplaces["id_emt_meetplaces"]];
        $codepais = $meetplace->emt_address["country_code"];
        
        $jsonResponse = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"],
            "proveedor" => $meet->emt_providers["id_emt_providers"],
            "provname" => $provname,
            "custname" => $custname,
            "lugar" => $meet->emt_meetplaces["fantasy_name"],
            "codigoPais" => $codepais
            );
        // foreach ($meet->emt_persons() as $person) {
        //     $jsonResponse = array(
        //     "name" => $person["first_name"],
        //     "email" => $person->emt_contacts["email"],
        //     "id" => $meet["id_emt_users"],
        //     "username" => $meet["username"]
        //     );
        // }

        echo json_encode($jsonResponse);
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

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
    // $customer = $db->emt_customers[$meet->emt_customers["id_emt_customers"]];
    //     foreach ($customer->emt_users() as $cust) {
    //         $custname = $cust["username"];
    //     }
    $jsonCustomers = array();
    foreach ($db->emt_meets()->where($meetByUserC) as $meet) {
        $jsonCustomers []  = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"]
        );
    }
    $jsonProviders = array();
    foreach ($db->emt_meets()->where($meetByUserP) as $meet) {
        $jsonProviders []  = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"]
        );
    }
    $jsonResponse = array();
    $jsonResponse [] = array(
            "custom" => $jsonCustomers,
            "provider" => $jsonProviders
        );
    echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsMeetCommon->run();