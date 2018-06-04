<?php
include 'UtilUser.php';
include 'UtilMeetPlace.php';
class UtilMeet
{

    public static function parseMeetDTO($meet,$provider,$customer,$meetplace) {
        foreach ($provider->emt_users() as $prov) {
            $userProvider = UtilUser::parseUserDTO($prov);
        }

        foreach ($customer->emt_users() as $cust) {
            $userCustomer = UtilUser::parseUserDTO($cust);
        }

        $place = UtilMeetPlace::parsePlaceDTO($meetplace);
        
        $jsonResponse = array(
            "id" => $meet["id_emt_meets"],
            "fecha" => $meet["date"],
            "userProvider" => $userProvider,
            "userCustomer" => $userCustomer,
            "meetplace" => $place
            );

        return $jsonResponse;
    }
}