<?php header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    //header('content-type: application/json; charset=utf-8');

//protected $allowed_http_methods = array('get', 'delete', 'post', 'put');
    
?>
<?php
require "Slim/Helper/Set.php";
require "Slim/Middleware.php";
require "Slim/Middleware/MethodOverride.php";
require "Slim/Middleware/PrettyExceptions.php";
require "Slim/Router.php";
require "Slim/Route.php";
require "Slim/Environment.php";
require "Slim/Http/Cookies.php";
require "Slim/Http/Response.php";
require "Slim/Http/Request.php";
require "Slim/Http/Headers.php";
require "Slim/Http/Util.php";
require "Slim/View.php";
require "Slim/Exception/Stop.php";
require "Slim/Slim.php";

require "Slim/Middleware/Flash.php";
require "Slim/Log.php";

require "notorm-master/notorm-master/NotORM.php";
 
$pdo = new PDO("mysql:dbname=p2000301_vender", "p2000301_vender", "wa71miZOfo");
$db = new NotORM($pdo);

// creamos una nueva instancia de Slim
$wsUserCommon = new \Slim\Slim();

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

$wsUserCommon->post("/insertUser", function () use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $user = $wsUserCommon->request()->post();
    echo json_encode($user);
    $result = $db->users->insert($user);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id"]));

});

$wsUserCommon->get("/deleteUser/:id", function ($id) use ($wsUserCommon, $db){    

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $users = $db->users()->where("id_user", $id);
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
 
$wsUserCommon->get("/getUserByID/:id", function ($id) use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $users = $db->users()->where("id_user", $id);
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

        echo json_encode($jsonResponse);
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "No existe un registro con el id $id "
            ));
    }

});

// agregamos una nueva ruta y un código
$wsUserCommon->get("/getAllUser", function () use ($wsUserCommon, $db){

    $wsUserCommon->response()->header("Content-Type", "application/json");
    
    $jsonResponse = array();
    foreach ($db->users() as $user) {
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
    }

    echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsUserCommon->run();