<?php
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $monthlyLimit = floatval($_POST['monthlyLimit'] ?? 0);

    if (empty($category) || $monthlyLimit <= 0) {
        header("Location: list.php?error=invalid_data");
        exit();
    }

    $std = $conn->prepare("SELECT idLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
    $std->bind_param("is", $userId, $category);
    $std->execute();
    $std->store_result();
    
    if ($std->num_rows > 0) {
        header("Location: list.php?error=limit_exists");
        exit();
    }
    $std->close();

    $stmt = $conn->prepare("INSERT INTO monthly_limits (idUser, category, monthlyLimit) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $userId, $category, $monthlyLimit);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=limit_added");
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