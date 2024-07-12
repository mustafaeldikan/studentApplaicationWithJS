<?php
$servername = "localhost";
$username = "mustafa";
$password = "1234";
$dbname = "sdb";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sid = $_GET['sid'];
$query = "DELETE FROM studentdb WHERE sid = $sid;";
if ($conn->query($query) === TRUE) {
    echo "1";
} else {
    echo "0";
}
$conn->close();
?>

