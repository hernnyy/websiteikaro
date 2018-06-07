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
            "contact" => UtilContact::parseContactDTO($person)
            );
        }
        return $json;
    }
}