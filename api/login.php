<?php

include_once './config/database.php';
require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$userName = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT id, user_name FROM users WHERE user_name='$userName' AND password='$password'";

$row = $conn->query($sql)->fetch();

if ($row['id'] > 0) {
    $secret_key = "YOUR_SECRET_KEY";
    $issuer_claim = "THE_ISSUER"; // this can be the servername
    $audience_claim = "THE_AUDIENCE";
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim + 10; //not before in seconds
    $expire_claim = $issuedat_claim + 300; // expire time in seconds
    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "id" => $row['id'],
            "username" => $row['user_name'],
        )
    );

    http_response_code(200);

    $jwt = JWT::encode($token, $secret_key);
    $_SESSION['token']=$jwt;
    echo json_encode(
        array(
            "access_token" => $jwt,
            "expire_in" => $expire_claim,
            "token_type" => "Bearer",
        )
    );
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Login failed."));
}
