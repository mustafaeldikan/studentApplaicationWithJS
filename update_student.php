<?php
$servername = "localhost";
$username = "mustafa";
$password = "1234";
$dbname = "sdb";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sid = $_POST['sid'];
$search_name = $_POST['isim'];
$search_lastName = $_POST['soyisim'];
$search_birthPlace = $_POST['dogumYeri'];
$search_birthDate = $_POST['dogumTarihi'];


$query = "UPDATE studentdb SET fname='$search_name', lname='$search_lastName', birthPlace='$search_birthPlace', birthDate='$search_birthDate' WHERE sid=$sid";

if ($conn->query($query) === TRUE) {
    echo "Kayıt Data base de başarıyla Saklandı";
} else {
    echo "Saklama sırasında bir hata oluştu !! " . $conn->error;
}

$conn->close();
?>
