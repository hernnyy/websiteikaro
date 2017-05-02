<?php

require "Slim/Slim.php";

use \Slim\Slim;

Slim::registerAutoloader();

// creamos una nueva instancia de Slim
$wsCommon = new Slim();

//http://ikaroira.com/ws-common.php/getCustomUrlIkaro/1
$wsCommon->get("/getCustomUrlIkaro/:id", function ($id) use ($wsCommon, $db){    

    $wsCommon->response()->header("Content-Type", "application/json; charset=utf-8");
    
    switch($id){
        case 1:
            echo json_encode(array(
            "status" => true,
            "url_custom" => "ikaroira.com",
            "message" => "Registro borrado exitosamente"
            ));
        break;
        case 2:
            echo json_encode(array(
            "status" => true,
            "url_custom" => "www.ikaroira.com",
            "message" => "Registro borrado exitosamente"
            ));
        break;
        case 3:
            echo json_encode(array(
            "status" => true,
            "url_custom" => "http://www.ikaroira.com",
            "message" => "Registro borrado exitosamente"
            ));
        break;
        default:
        echo json_encode(array(
            "status" => false,
            "message" => "El Registro con id $id no existe"
            ));
    }

});


// corremos la aplicación
$wsCommon->run();