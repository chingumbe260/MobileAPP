<?phpinclude 'db_config.php';

$sql = "SELECT id, username as name FROM users";
$result = $conn->query($sql);

$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
