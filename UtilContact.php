<?php
class UtilContact
{
    public static function parseContactDTO($object) {
        if ($object->emt_contacts["id_emt_contacts"]) {
            return array(
                "id" => $object->emt_contacts["id_emt_contacts"],
                "email" => $object->emt_contacts["email"],
                "email2" => $object->emt_contacts["email2"],
                "cellphone" => $object->emt_contacts["cellphone"],
                "cellphone2" => $object->emt_contacts["cellphone2"]
                );
        }
        return null;
    }
}