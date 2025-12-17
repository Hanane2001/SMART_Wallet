<?php
session_start();
include '../config/database.php';
$userId = checkAuth();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amountEx'] ?? 0);
    $date = $_POST['dateEx'] ?? '';
    $description = trim($_POST['descriptionEx'] ?? '');
    $idCard = intval($_POST['idCard'] ?? 0);
    $category = trim($_POST['category'] ?? 'Other');

    if ($amount <= 0 || empty($date) || $idCard <= 0 || empty($category)) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $std = $conn->prepare("SELECT currentBalance FROM cards WHERE idCard = ? AND idUser = ?");
    $std->bind_param("ii", $idCard, $userId);
    $std->execute();
    $resC = $std->get_result();
    
    if ($resC->num_rows === 0) {
        header("Location: list.php?error=invalid_card");
        exit();
    }
    $card = $resC->fetch_assoc();
    $std->close();
 
    if ($amount > $card['currentBalance']) {
        header("Location: list.php?error=insufficient_funds");
        exit();
    }

    $month = date('Y-m');
    $str = $conn->prepare("SELECT monthlyLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
    $str->bind_param("is", $userId, $category);
    $str->execute();
    $resL = $str->get_result();
    
    if ($resL->num_rows > 0) {
        $limit = $resL->fetch_assoc();
        $monthL = $limit['monthlyLimit'];

        $resS = $conn->prepare("SELECT SUM(amountEx) as total 
            FROM expenses 
            WHERE idUser = ? 
            AND category = ? 
            AND DATE_FORMAT(dateEx, '%Y-%m') = ?");
        $resS->bind_param("iss", $userId, $category, $month);
        $resS->execute();
        $spentResult = $resS->get_result();
        $spent = $spentResult->fetch_assoc()['total'] ?? 0;
        $resS->close();
        
        if (($spent + $amount) > $monthL) {
            header("Location: list.php?error=limit_exceeded");
            exit();
        }
    }
    $str->close();

    $stmt = $conn->prepare("INSERT INTO expenses (idUser, idCard, amountEx, dateEx, descriptionEx, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsss", $userId, $idCard, $amount, $date, $description, $category);
    
    if ($stmt->execute()) {
        $conn->query("UPDATE cards SET currentBalance = currentBalance - $amount WHERE idCard = $idCard");
        
        header("Location: list.php?message=expense_added");
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