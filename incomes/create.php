<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amountIn'] ?? 0);
    $date = $_POST['dateIn'] ?? '';
    $description = trim($_POST['descriptionIn'] ?? '');
    $idCard = intval($_POST['idCard'] ?? 0);

    if ($amount <= 0 || empty($date) || $idCard <= 0) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $std = $conn->prepare("SELECT idCard FROM cards WHERE idCard = ? AND idUser = ?");
    $std->bind_param("ii", $idCard, $userId);
    $std->execute();
    $cardResult = $std->get_result();
    
    if ($cardResult->num_rows === 0) {
        header("Location: list.php?error=invalid_card");
        exit();
    }
    $std->close();

    $str = $conn->prepare("INSERT INTO incomes (idUser, idCard, amountIn, dateIn, descriptionIn) VALUES (?, ?, ?, ?, ?)");
    $str->bind_param("iidss", $userId, $idCard, $amount, $date, $description);
    
    if ($str->execute()) {
        $conn->query("UPDATE cards SET currentBalance = currentBalance + $amount WHERE idCard = $idCard");
        
        header("Location: list.php?message=income_added");
    } else {
        header("Location: list.php?error=insert_failed");
    }
    $str->close();
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>
