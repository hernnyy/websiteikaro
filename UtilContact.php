<?php
class UtilContact
{
    public static function parseContactDTO($object) {
        if ($person->emt_contacts["id_emt_contacts"]) {
            return array(
                "id" => $person->emt_contacts["id_emt_contacts"],
                "email" => $person->emt_contacts["email"],
                "email2" => $person->emt_contacts["email2"],
                "cellphone" => $person->emt_contacts["cellphone"],
                "cellphone2" => $person->emt_contacts["cellphone2"]
                );
        }
        return null;
    }
}