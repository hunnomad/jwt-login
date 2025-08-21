<?php

$key = $_ENV["JWT_SECRET_KEY"];
$issued_at = time();
$expiration_time = $issued_at + (60 * 60); // valid for 1 hour

?>
