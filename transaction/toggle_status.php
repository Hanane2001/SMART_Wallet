<?php
include '../config/database.php';
$userId = checkAuth();

header('Content-Type: application/json');

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$id = intval($_GET['id']);
$status = intval($_GET['status']);

$check = $conn->prepare("SELECT idRecurring FROM recurring_transactions WHERE idRecurring = ? AND idUser = ?");
$check->bind_param("ii", $id, $userId);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Transaction not found']);
    exit();
}
$check->close();

$stmt = $conn->prepare("UPDATE recurring_transactions SET is_active = ? WHERE idRecurring = ?");
$stmt->bind_param("ii", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>