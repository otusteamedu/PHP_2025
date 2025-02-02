<? 

$conn = new mysqli("mysql", "root", "s123123");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

else {
    echo "Соединени с сервером MySQL установлено";
}
 