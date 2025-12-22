<?php
include '../config/database.php';
$userId = checkAuth();

$recurring_result = $conn->query("SELECT rt.*, c.cardName 
FROM recurring_transactions rt
JOIN cards c ON rt.idCard = c.idCard
WHERE rt.idUser = $userId
ORDER BY rt.day_of_month, rt.created_at DESC
");

$cards_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurring Transactions - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                    <span class="text-white text-xl font-bold">SmartBudget</span>
                </div>
                <div id="navLinks" class="hidden md:flex space-x-6">
                    <a href="../dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="../incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="../cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="../transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                    <a href="../limits/list.php" class="text-white hover:text-blue-200">Limits</a>
                    <a href="list.php" class="text-white font-bold">Transaction</a>
                    <a href="../auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Recurring Transactions</h1>
            </div>
            <button onclick="showAddForm()" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">Add Recurring</button>
        </div>

        <div id="addForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add Recurring Transaction</h2>
            <form action="create.php" method="POST" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Transaction Type</label>
                        <select name="transaction_type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Day of Month</label>
                        <input type="number" min="1" max="31" name="day_of_month" required 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="1-31">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Card</label>
                        <select name="idCard" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a card</option>
                            <?php while($card = $cards_result->fetch_assoc()): ?>
                            <option value="<?php echo $card['idCard']; ?>">
                                <?php echo htmlspecialchars($card['cardName']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" required 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Salary, Rent, Internet Bill, etc.">
                    </div>
                    <div id="categoryField" class="hidden">
                        <label class="block text-gray-700 mb-2">Category (for expenses)</label>
                        <select name="category" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Other">Other</option>
                            <option value="Food">Food</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Housing">Housing</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Education">Education</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" checked class="mr-2">
                    <label for="is_active" class="text-gray-700">Active</label>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Save</button>
                    <button type="button" onclick="hideAddForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Card</th>
                            <th class="px-6 py-3 text-left">Day</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Last Processed</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recurring_result->num_rows > 0): ?>
                            <?php while($transaction = $recurring_result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo htmlspecialchars($transaction['description']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($transaction['transaction_type'] == 'income'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Income</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Expense</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-semibold <?php echo $transaction['transaction_type'] == 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                                    $<?php echo number_format($transaction['amount'], 2); ?>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($transaction['cardName']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        Day <?php echo $transaction['day_of_month']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($transaction['is_active']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $transaction['last_processed'] ? date('Y-m-d', strtotime($transaction['last_processed'])) : 'Never'; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button onclick="toggleStatus(<?php echo $transaction['idRecurring']; ?>, <?php echo $transaction['is_active'] ? 0 : 1; ?>)" 
                                                class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200 transition text-sm">
                                            <?php echo $transaction['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                        <button onclick="if(confirm('Delete this recurring transaction?')) window.location.href='delete.php?id=<?php echo $transaction['idRecurring']; ?>'" 
                                                class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition text-sm">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <p class="mb-2">No recurring transactions set up yet.</p>
                                    <p>Add recurring transactions to automate your monthly finances.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addForm').classList.remove('hidden');
        }
        
        function hideAddForm() {
            document.getElementById('addForm').classList.add('hidden');
        }

        document.querySelector('select[name="transaction_type"]').addEventListener('change', function() {
            const categoryField = document.getElementById('categoryField');
            if(this.value === 'expense') {
                categoryField.classList.remove('hidden');
                categoryField.querySelector('select').required = true;
            } else {
                categoryField.classList.add('hidden');
                categoryField.querySelector('select').required = false;
            }
        });
        
        function toggleStatus(id, newStatus) {
            fetch('toggle_status.php?id=' + id + '&status=' + newStatus)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>