<?php
session_start();

// Check if the user is logged in and has the necessary permissions
if (!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

require_once '../classes/account.class.php';

$accountObj = new Account();

$roleFilter = $searchTerm = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Sanitize input from the search form
    $searchTerm = htmlentities($_POST['search']);
    $roleFilter = htmlentities($_POST['role']);
}

$accounts = $accountObj->getAllAccounts($roleFilter, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .search-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .add-btn {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 4px;
            float: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f8f9fa;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #f1f1f1;
        }
        .action-links a {
            text-decoration: none;
            padding: 3px 8px;
            border-radius: 3px;
            margin-right: 5px;
        }
        .edit-btn {
            border: 1px solid #28a745;
            color: #28a745;
        }
        .delete-btn {
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        p.search {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <input type="text" name="search" id="search" placeholder="Search accounts..." value="<?= $searchTerm ?>">
        <label>Category</label>
        <select name="role" id="role">
            <option value="">Choose...</option>
            <option value="admin" <?= ($roleFilter == 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="staff" <?= ($roleFilter == 'staff') ? 'selected' : '' ?>>Staff</option>
        </select>
        <input type="submit" value="Search" name="search_btn">
        <a href="addaccount.php" class="add-btn">Add Account</a>
    </div>

    <table>
        <tr>
            <th>No.</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        
        <?php
        $i = 1;
        if (empty($accounts)) {
        ?>
            <tr>
                <td colspan="6"><p class="search">No accounts found.</p></td>
            </tr>
        <?php
        }
        foreach ($accounts as $account) {
        ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($account['first_name']) ?></td>
                <td><?= htmlspecialchars($account['last_name']) ?></td>
                <td><?= htmlspecialchars($account['username']) ?></td>
                <td><?= htmlspecialchars($account['role']) ?></td>
                <td class="action-links">
                    <a href="editaccount.php?id=<?= $account['id'] ?>" class="edit-btn">Edit</a>
                    <?php if ($_SESSION['account']['is_admin']) { ?>
                        <a href="#" class="delete-btn deleteBtn" 
                           data-id="<?= $account['id'] ?>" 
                           data-name="<?= htmlspecialchars($account['username']) ?>">Delete</a>
                    <?php } ?>
                </td>
            </tr>
        <?php
            $i++;
        }
        ?>
    </table>

    <script>
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                let username = this.dataset.name;
                let accountId = this.dataset.id;
                let response = confirm("Do you want to delete the account " + username + "?");
                if (response) {
                    fetch('deleteaccount.php?id=' + accountId, {
                        method: 'GET'
                    })
                    .then(response => response.text())
                    .then(data => {
                        if(data === 'success') {
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
