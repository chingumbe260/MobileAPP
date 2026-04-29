<?php
include 'db_config.php';
header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    // Prepare statement to fetch user
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Check if password matches the hash in database
        if (password_verify($password, $user['password'])) {
            // SUCCESS: Return status "success" and user details
            echo json_encode([
                "status" => "success",
                "token" => "dummy-sample-token",
                "user" => [
                    "id" => (int)$user['id'],
                    "name" => $user['username']
                ]
            ]);
        } else {
            // ERROR: Wrong password
            echo json_encode([
                "status" => "error",
                "message" => "Invalid password"
            ]);
        }
    } else {
        // ERROR: User not found
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }
    $stmt->close();
} else {
    // ERROR: Missing input
    echo json_encode([
        "status" => "error",
        "message" => "Missing email or password"
    ]);
}
?>