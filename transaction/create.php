<?php
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_type = trim($_POST['transaction_type'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $idCard = intval($_POST['idCard'] ?? 0);
    $day_of_month = intval($_POST['day_of_month'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $category = $transaction_type == 'expense' ? trim($_POST['category'] ?? 'Other') : '';

    if (empty($transaction_type) || $amount <= 0 || empty($description) || $idCard <= 0 || $day_of_month < 1 || $day_of_month > 31) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $card_check = $conn->prepare("SELECT idCard FROM cards WHERE idCard = ? AND idUser = ?");
    $card_check->bind_param("ii", $idCard, $userId);
    $card_check->execute();
    
    if ($card_check->get_result()->num_rows === 0) {
        header("Location: list.php?error=invalid_card");
        exit();
    }
    $card_check->close();

    $stmt = $conn->prepare("INSERT INTO recurring_transactions(idUser, idCard, transaction_type, amount, description, category, day_of_month, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdssii", $userId, $idCard, $transaction_type, $amount, $description, $category, $day_of_month, $is_active);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=recurring_added");
    } else {
        header("Location: list.php?error=insert_failed");
    }
    $stmt->close();
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>