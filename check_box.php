<?php
require 'auth.php';
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['completed'])) {
    $id = $conn->real_escape_string($data['id']);
    $completed = $conn->real_escape_string($data['completed']);

    $sql = "UPDATE tasks SET completed = $completed WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}