<?php
include 'UtilPerson.php';
class UtilUser
{
    // Declaración de una propiedad
    public $var = 'un valor predeterminado';

    public static function parseUserDTO($user) {
        foreach ($user->emt_persons() as $person) {
            $jsonPerson = UtilPerson::parsePersonDTO($person);
        }
        $jsonResponse = array(
            "id" => $user["id_emt_users"],
            "username" => $user["username"],
            "customer" => UtilUser::parseCustomerFromUserDTO($user),
            "provider" => UtilUser::parseProviderFromUserDTO($user),
            "person" => $jsonPerson,
            "isValid" => true,
            "status" => true,
            "message" => "User OK"
            );
        return $jsonResponse;
    }

    public static function parseProviderFromUserDTO($user) {
        if ($user->emt_providers["id_emt_providers"]) {
            return array(
                "id" => $user->emt_providers["id_emt_providers"],
                "dots" => $user->emt_providers["dots"]
                );
        }
        return null;
    }

    public static function parseCustomerFromUserDTO($user) {
        if ($user->emt_customers["id_emt_customers"]) {
            return array(
                "id" => $user->emt_customers["id_emt_customers"],
                "dots" => $user->emt_customers["dots"]
                );
        }
        return null;
    }

}