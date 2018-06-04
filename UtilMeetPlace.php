<?php
class UtilMeetPlace
{

    public static function parsePlaceDTO($place) {
        foreach ($place->emt_contacts() as $contact) {
            $jsonContact = UtilContact::parseContactDTO($contact);
        }
        $jsonResponse = array(
            "id" => $place["id_emt_meetplaces"],
            "fantasy_name" => $place["fantasy_name"],
            "status" => $place["status"],
            "name" => $place["name"],
            "addres" => UtilMeetPlace::parseAddresDTO($place),
            "contact" => $jsonContact,
            "isValid" => true,
            "status" => true,
            "message" => "Place OK"
            );
        return $jsonResponse;
    }

    public static function parseAddresDTO($object) {
        if ($object->emt_address["id_emt_address"]) {
            $jsonResponse = array(
                "id" => $object->emt_address["id_emt_address"],
                "country_code" => $object->emt_address["country_code"],
                "country" => $object->emt_address["country"],
                "street_name" => $object->emt_address["street_name"],
                "street_number" => $object->emt_address["street_number"],
                "postal_code" => $object->emt_address["postal_code"],
                "locality" => $object->emt_address["locality"],
                "region" => $object->emt_address["region"]
                );
        }
        return $jsonResponse;
    }
}