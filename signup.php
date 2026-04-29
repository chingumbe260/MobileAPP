<?php
header('Content-Type: application/json');
include 'db_config.php';

$data = json_decode(file_get_contents("php://input"), true);

// Check if all required fields are present
if (isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['confirm_password'])) {
    
    $user = $data['username'];
    $email = $data['email'];
    $password = $data['password'];
    $confirm_password = $data['confirm_password'];

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }

    // 2. Hash the password securely
    $pass_hashed = password_hash($password, PASSWORD_DEFAULT);

    // 3. Prepare statement to insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $pass_hashed);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } else {
        // This usually happens if the email is marked as UNIQUE in the database
        echo json_encode(["status" => "error", "message" => "Email already exists or registration failed"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
}
