<?php

// Gets request parameters
$name = isset($_GET['name']) ? $_GET['name'] : null;
$count = isset($_GET['count']) ? $_GET['count'] : null;

// Tries to connect to mysql database
$db_connection = mysqli_connect("localhost", "root", "", "zinutes");
if (!$db_connection)
    die("Mysql database unreachable: " . mysqli_error($db_connection));

// Builds sql based on given parameters
$sql = "";
if ($name == null) {
    $sql = "SELECT id, name, receiver_email, date, message FROM messages ORDER BY id DESC";
} else {
    $sql = "SELECT id, name, receiver_email, date, message FROM messages WHERE name = '{$name}' ORDER BY id DESC";
}

if ($count != null) {
    $sql .= " LIMIT {$count}";
}

// Fetches data from database, converts to json
$results = $db_connection->query($sql);
$response = $results->fetch_all(MYSQLI_ASSOC);
echo json_encode($response);