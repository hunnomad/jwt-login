<?php
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------

date_default_timezone_set("Europe/Budapest");

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include db params and own classes
use HunNomad\Database\Database;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

$user = new classUser($connect);

$data = json_decode(file_get_contents("php://input"));
$jwt=isset($data->jwt) ? $data->jwt : "";

if($jwt)
{
    try
    {
        $decoded = JWT::decode($jwt, new Key($key, 'HS512'));
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        if($user->update())
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
            $jwt = JWT::encode($token, $key,'HS512');
            http_response_code(200);
            echo json_encode(
                [
                    "message" => "User was updated.",
                    "jwt" => $jwt
                ]
            );
        }
        else
        {
            http_response_code(401);
            echo json_encode(array("message" => "Unable to update user."));
        }
    }
    catch (Exception $e)
    {
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ]);
    }
}
