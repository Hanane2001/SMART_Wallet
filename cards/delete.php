<?php
include '../config/database.php';
$userId = checkAuth();
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: list.php?error=no_id");
    exit();
}
$cardId = intval($_GET['id']);
$std = $conn->prepare("SELECT isMain FROM cards WHERE idCard = ? AND idUser = ?");
$std->bind_param("ii",$cardId,$userId);
$std->execute();
$res = $std->get_result();
if($res->num_rows === 0){
    header("Location: list.php?error=not_found");
    exit();
}
$card = $res->fetch_assoc();
if($card['isMain']){
    header("Location: list.php?error=cannot_delete_main");
    exit();
}
$str = $conn->prepare("DELETE FROM cards WHERE idCard = ? AND idUser = ?");
$str->bind_param("ii",$cardId,$userId);
if($str->execute()){
    header("Location: list.php?message=card_deleted");
}else{
    header("Location: list.php?error=delete_failed");
}
$str->close();
$conn->close();
exit();
?>