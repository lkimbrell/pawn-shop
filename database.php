<?php

$connection = new mysqli("localhost", "root", "", "pawn_shop");

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

?>