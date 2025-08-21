<?php
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------

date_default_timezone_set("Europe/Budapest");

// required headers
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include db params and own classes
use HunNomad\Database\Database;
use \Firebase\JWT\JWT;

require_once __DIR__."/../autoloader.php";
require_once __DIR__."/../vendor/autoload.php";

# Load .env enviroment ---------------------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../");
$dotenv->safeLoad();
# Load .env enviroment ---------------------------------------------------------------

# Database Connection ----------------------------------------------------------------
$database = new Database($_ENV['MYSQL_HOST'],$_ENV['MYSQL_DB'],$_ENV['MYSQL_USER'],$_ENV['MYSQL_PASS'],$_ENV['MYSQL_PORT']);
$connect  = $database->getConnection();
# Database Connection ----------------------------------------------------------------

require_once __DIR__."/../config/jwt_config.php";

// required headers
header("Access-Control-Allow-Origin: ".$_ENV["APP_HOST"]."");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// instantiate product object
$user = new classUser($connect);

// get posted data
$data = json_decode(file_get_contents("php://input"));
// set product property values
$user->email = $data->email;
$email_exists = $user->emailExists();

// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $user->password))
{
    $token = [
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $_ENV["APP_HOST"],
       "data" => [
           "id" => $user->id,
           "firstname" => $user->firstname,
           "lastname" => $user->lastname,
           "email" => $user->email
       ]
    ];
    // set response code
    http_response_code(200);
    // generate jwt
    $jwt = JWT::encode($token, $key,'HS512');
    echo json_encode(
        [
            "message" => "Successful login.",
            "jwt" => $jwt
        ]
    );
}
else{
    // set response code
    http_response_code(401);
    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
