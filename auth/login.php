<?php
include '../config/database.php';
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)){
        $errors[] = "Email and password are required";
    }

    if(empty($errors)){
        $std = $conn->prepare("SELECT idUser, fullName, email, password FROM users WHERE email = ?");
        $std->bind_param("s", $email);
        $std->execute();
        $result = $std->get_result();
        
        if($result->num_rows === 1){
            $user = $result->fetch_assoc();
            
            if(password_verify($password, $user['password'])){
                $_SESSION['pending_user_id'] = $user['idUser'];
                $_SESSION['pending_email']   = $user['email'];
                $_SESSION['pending_name']    = $user['fullName'];
                header("Location: email_send.php");
                exit();
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            $errors[] = "Invalid email or password";
        }
        $std->close();
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-wallet text-blue-600 text-5xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
                <p class="mt-2 text-center text-sm text-gray-600">Or <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">create a new account</a></p>
            </div>

            <!-- Afficher les erreurs -->
            <?php 
            $err = [];
            if(isset($_SESSION['errors'])) {
                $err = $_SESSION['errors'];
                unset($_SESSION['errors']);
            } elseif(!empty($errors)) {
                $err = $errors;
            }
            if(!empty($err)): 
            ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    <?php foreach($err as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="login.php" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Sign In</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-blue-600 hover:text-blue-500"><i class="fas fa-arrow-left mr-2"></i>Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>


<?php 
 closeConnection($conn); ?>
