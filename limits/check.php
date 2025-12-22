<?php
include '../config/database.php';
checkAuth();
$userId = $_SESSION['user_id'];
$category = $_GET['category'] ?? '';
$amount = floatval($_GET['amount'] ?? 0);

if (empty($category) || $amount <= 0) {
    echo json_encode(['warning' => false]);
    exit();
}

$monthQ = date('Y-m');
$limitQ = $conn->prepare("SELECT monthlyLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
$limitQ->bind_param("is", $userId, $category);
$limitQ->execute();
$limitResult = $limitQ->get_result();

if ($limitResult->num_rows === 0) {
    echo json_encode(['warning' => false]);
    exit();
}

$limit = $limitResult->fetch_assoc();
$limitQ->close();

$spentQ = $conn->prepare("SELECT SUM(amountEx) as total 
FROM expenses 
WHERE idUser = ? 
AND category = ? 
AND DATE_FORMAT(dateEx, '%Y-%m') = ?");
$spentQ->bind_param("iss", $userId, $category, $monthQ);
$spentQ->execute();
$spentResult = $spentQ->get_result();
$spent = $spentResult->fetch_assoc()['total'] ?? 0;
$spentQ->close();

$warning = ($spent + $amount) > $limit['monthlyLimit'];

echo json_encode([
    'warning' => $warning,
    'spent' => $spent,
    'limit' => $limit['monthlyLimit'],
    'remaining' => $limit['monthlyLimit'] - $spent
]);

$conn->close();
?>