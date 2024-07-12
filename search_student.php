<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "mustafa";
$password = "1234";
$dbname = "sdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search_name = $_POST['isim'];
$search_lastName = $_POST['soyisim'];
$search_birthPlace = $_POST['dogumYeri'];
$search_birthDate = $_POST['dogumTarihi'];

$query = "SELECT * FROM studentdb WHERE fname LIKE '%" . $search_name . "%' AND lname LIKE '%" . $search_lastName . "%' AND birthPlace LIKE '%" . $search_birthPlace . "%' AND birthDate LIKE '%" . $search_birthDate . "%'  ";

$result = $conn->query($query);

$new_array = [];
if ($result) {
    if ($result->num_rows > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $new_array[] = $row;
        }
    }
    echo json_encode($new_array);
} else {
    echo json_encode(["error" => $conn->error]);
}

$conn->close();
?>
