<?php
include '../config/database.php';
$userId = checkAuth();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$id = intval($_GET['id']);

$check = $conn->prepare("SELECT idRecurring FROM recurring_transactions WHERE idRecurring = ? AND idUser = ?");
$check->bind_param("ii", $id, $userId);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    header("Location: list.php?error=not_found");
    exit();
}
$check->close();

$stmt = $conn->prepare("DELETE FROM recurring_transactions WHERE idRecurring = ? AND idUser = ?");
$stmt->bind_param("ii", $id, $userId);

if ($stmt->execute()) {
    header("Location: list.php?message=recurring_deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit();
?>