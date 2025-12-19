<?php 
include '../config/database.php';
session_start();

if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['otp'])) {
    header("Location: email_send.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userOtp = $_POST['otp'] ?? '';
    
    if (empty($userOtp)) {
        $error = "Please enter OTP";
    } elseif ($userOtp !== $_SESSION['otp']) {
        $error = "Invalid OTP code";
    } else {
        unset($_SESSION['otp'], $_SESSION['otp_time']);

        $_SESSION['user_id']    = $_SESSION['pending_user_id'];
        $_SESSION['user_email'] = $_SESSION['pending_email'];
        $_SESSION['user_name']  = $_SESSION['pending_name'];

        unset(
            $_SESSION['pending_user_id'],
            $_SESSION['pending_email'],
            $_SESSION['pending_name']
        );
        
        $_SESSION['login_success'] = true;
        header("Location: ../dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-shield-alt text-blue-600 text-5xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Two-Factor Authentication</h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter the code sent to <?php echo htmlspecialchars($_SESSION['pending_email'] ?? ''); ?>
                </p>
                
                <?php if(isset($error)): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <form class="mt-8 space-y-6" action="verify_otp.php" method="POST">
                <div>
                    <label for="otp" class="block text-gray-700 mb-2">OTP Code (6 digits)</label>
                    <input id="otp" name="otp" type="text" maxlength="6" pattern="\d{6}" required 
                           class="appearance-none rounded-lg relative block w-full px-3 py-3 text-center text-2xl font-bold border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Verify OTP
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="email_send.php?resend=1" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-redo mr-2"></i>Resend OTP
                    </a>
                    <span class="mx-2">|</span>
                    <a href="login.php" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php closeConnection($conn); ?>