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
 
$pdo = new PDO("mysql:dbname=u693453499_dev", "u693453499_user", "minoro456");
$db = new NotORM($pdo);

// creamos una nueva instancia de Slim
$wsFormPromo = new \Slim\Slim();

$wsFormPromo->post("/insertForm", function () use ($wsFormPromo, $db){    

    $wsFormPromo->response()->header("Content-Type", "application/json");
    
    $form = $wsFormPromo->request()->post();
    echo json_encode($form);
    $result = $db->forms_app_default->insert($form);
    echo json_encode(array(
            "status" => true,
            "message" => "Registro guardado exitosamente",
            "error" => $result,
            "id" => $result["id"]));

});

$wsFormPromo->get("/deleteForm/:id", function ($id) use ($wsFormPromo, $db){    

    $wsFormPromo->response()->header("Content-Type", "application/json");
    
    $form = $db->forms_app_default()->where("id", $id);
    if ($form->fetch()) {
        $result = $form->delete();
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
 
$wsFormPromo->get("/getFormByID/:id", function ($id) use ($wsFormPromo, $db){

    $wsFormPromo->response()->header("Content-Type", "application/json");
    
    $form = $db->forms_app_default()->where("id", $id);
    if ($forms_app_default = $form->fetch()) {
        $jsonResponse []  = array(
            "id" => $forms_app_default["id"],
            "firstname" => $forms_app_default["firstname"],
            "lastname" => $forms_app_default["lastname"],
            "email" => $forms_app_default["email"],
            "email2" => $forms_app_default["email2"],
            "type_id" => $forms_app_default["type_id"],
            "number_id" => $forms_app_default["number_id"],
            "birthday" => $forms_app_default["birthday"],
            "country" => $forms_app_default["country"],
            "province" => $forms_app_default["province"],
            "locality" => $forms_app_default["locality"],
            "date_modification" => $forms_app_default["date_modification"],
            "user_modification" => $forms_app_default["user_modification"],
            "code_verification" => $forms_app_default["code_verification"],
            "code_validation" => $forms_app_default["code_validation"],
            "code_aprobation" => $forms_app_default["code_aprobation"],
            "state" => $forms_app_default["state"],
            "obs" => $forms_app_default["obs"]
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
$wsFormPromo->get("/getAllForm", function () use ($wsFormPromo, $db){

	$wsFormPromo->response()->header("Content-Type", "application/json");
	
    $jsonResponse = array();
    foreach ($db->forms_app_default() as $forms_app_default) {
        $jsonResponse []  = array(
            "id" => $forms_app_default["id"],
            "firstname" => $forms_app_default["firstname"],
            "lastname" => $forms_app_default["lastname"],
            "email" => $forms_app_default["email"],
            "email2" => $forms_app_default["email2"],
            "type_id" => $forms_app_default["type_id"],
            "number_id" => $forms_app_default["number_id"],
            "birthday" => $forms_app_default["birthday"],
            "country" => $forms_app_default["country"],
            "province" => $forms_app_default["province"],
            "locality" => $forms_app_default["locality"],
            "date_modification" => $forms_app_default["date_modification"],
            "user_modification" => $forms_app_default["user_modification"],
            "code_verification" => $forms_app_default["code_verification"],
            "code_validation" => $forms_app_default["code_validation"],
            "code_aprobation" => $forms_app_default["code_aprobation"],
            "state" => $forms_app_default["state"],
            "obs" => $forms_app_default["obs"]
        );
    }

	echo json_encode($jsonResponse);

});
 
// corremos la aplicación
$wsFormPromo->run();