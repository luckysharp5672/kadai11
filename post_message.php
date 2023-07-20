<?php
session_start();
require 'dbconnect.php';

// Get the user ID, message, and board ID from the POST data
$user_id = $_SESSION['user_id'];
$message = $_POST['message'];
$board_id = $_SESSION['board_id'];

$query = "INSERT INTO messages (user_id, message, board_id) VALUES (:user_id, :message, :board_id)";
$statement = $pdo->prepare($query);
$statement->execute([
    ':user_id' => $user_id,
    ':message' => $message,
    ':board_id' => $board_id
]);

$response = ['status' => 'success', 'message' => $message];

header('Content-Type: application/json');
echo json_encode($response);
?>
