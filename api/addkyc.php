<?php
include_once './config/database.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$secret_key = "YOUR_SECRET_KEY";
$jwt = null;


$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

$arr = explode(" ", $authHeader);

$jwt = $arr[1];

if ($jwt) {

    try {

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        $data = json_decode(file_get_contents("php://input"), true);
        $firstName = $data['ind_first_name'];
        $lastName = $data['ind_last_name'];
        $dob = date('Y-m-d', strtotime($data['ind_dob']));
        $email = $data['ind_email'];
        $phoneNumber = $data['ind_phone'];
        $address = $data['ind_address1'];
        $country = $data['ind_country_residence'];
        $state = $data['ind_state'];
        $city = $data['city'];
        $pincode = $data['ind_pincode'];

        $sql = "INSERT INTO `kyc` (`first_name`, `last_name`, `dob`, `email`, `phone_number`, `address`, `city`, `state`, `country`, `pincode`) VALUES ('$firstName','$lastName','$dob','$email','$phoneNumber','$address','$city','$state','$country','$pincode')";

        if ($conn->exec($sql)) {
            echo json_encode(array(
                "message" => "Record Added",
            ));
        } else {
            echo json_encode(array(
                "message" => "Record Not Added",
            ));
        }
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied. Please Login To Add KYC Data",
            "error" => $e->getMessage()
        ));
    }
}
