<?php

# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------

date_default_timezone_set("Europe/Budapest");

// Include db params and own classes
use HunNomad\Database\Database;

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
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->password = $data->password;
// use the create() method here
// create the user
if(
    !empty($user->firstname) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
){
    // set response code
    http_response_code(200);
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
// message if unable to create user
else{
    // set response code
    http_response_code(400);
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>
