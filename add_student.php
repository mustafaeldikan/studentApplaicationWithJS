<?php

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

$query = "INSERT INTO studentdb (fname, lname, birthPlace, birthDate) VALUES ('$search_name', '$search_lastName', '$search_birthPlace', '$search_birthDate')";

if ($conn->query($query) === TRUE) {
    $id = mysqli_insert_id($conn);
    $body = ["message" => "Öğrenci başarıyla eklendi.", "data" => [
        "id"=>$id,
        "isim"=>$search_name,
        "soyisim"=>$search_lastName,
        "dogumYeri"=>$search_birthPlace,
        "dogumTarihi"=>$search_birthDate,
    ]];
    echo json_encode($body);
} else {
    echo "Ekleme sırasında bir hata oluştu !! " . $conn->error;
}
$conn->close();
?>
