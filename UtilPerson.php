<?php
include 'UtilContact.php';
class UtilPerson
{

    public static function parsePersonDTO($person) {
        if ($person) {
            $json = array(
            "id" => $person["id_emt_persons"],
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
        return $json;
    }
}