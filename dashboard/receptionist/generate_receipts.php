<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Receptionist');

$receptionist_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int) ($_POST['member_id'] ?? 0);
    $plan_id = (int) ($_POST['plan_id'] ?? 0);
    $amount_paid = (float) ($_POST['amount_paid'] ?? 0);
    $date_of_purchase = $_POST['date_of_purchase'] ?? '';
    $today = date('Y-m-d');

    $date_ok = DateTime::createFromFormat('Y-m-d', $date_of_purchase) !== false;

    if ($member_id <= 0 || $plan_id <= 0) {
        $error = 'Pick a member and a plan.';
    } elseif ($amount_paid <= 0) {
        $error = 'Amount paid must be more than zero.';
    } elseif (!$date_ok || $date_of_purchase > $today) {
        $error = 'Purchase date must be a real date, not in the future.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO receipt (Member_id, Generated_by, Plan_id, Amount_paid, Date_of_creation, Date_of_purchase) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidss", $member_id, $receptionist_id, $plan_id, $amount_paid, $today, $date_of_purchase);
            $stmt->execute();
            $receipt_no = $stmt->insert_id;

            // The paid plan becomes the member's current plan (shown on their dashboard).
            $stmt = $conn->prepare("DELETE FROM uses WHERE Member_ID = ?");
            $stmt->bind_param("i", $member_id);
            $stmt->execute();
            $stmt = $conn->prepare("INSERT INTO uses (Plan_ID, Member_ID) VALUES (?, ?)");
            $stmt->bind_param("ii", $plan_id, $member_id);
            $stmt->execute();

            $conn->commit();
            $_SESSION['success_message'] = "Receipt #$receipt_no generated.";
            header('Location: generate_receipts.php');
            exit();
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            $error = 'Could not generate receipt: ' . $ex->getMessage();
        }
    }
}

$members = $conn->query("SELECT u.ID, u.Name FROM user u JOIN member m ON m.Member_ID = u.ID ORDER BY u.Name");
$plans = $conn->query("SELECT Plan_id, Name, Amount FROM membership_plan ORDER BY Duration");
$receipts = $conn->query("
    SELECT r.Receipt_no, u.Name AS Member_Name, mp.Name AS Plan_Name, r.Amount_paid,
           r.Date_of_purchase, r.Date_of_creation, g.Name AS Issued_By
    FROM receipt r
    JOIN user u ON r.Member_id = u.ID
    JOIN membership_plan mp ON r.Plan_id = mp.Plan_id
    LEFT JOIN user g ON r.Generated_by = g.ID
    ORDER BY r.Receipt_no DESC
    LIMIT 50
");

$selected_member = (int) ($_POST['member_id'] ?? $_GET['member_id'] ?? 0);
$selected_plan = (int) ($_POST['plan_id'] ?? 0);
$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate Receipt | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script>
        // Pre-fill the amount with the plan's price.
        function updateAmount(select) {
            const amount = select.options[select.selectedIndex].dataset.amount;
            if (amount) document.getElementById('amount_paid').value = amount;
        }
    </script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Generate Receipt</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Member:</label>
                    <select name="member_id" class="form-control" required>
                        <option value="">-- Select Member --</option>
                        <?php while ($m = $members->fetch_assoc()): ?>
                            <option value="<?= e($m['ID']) ?>" <?= $selected_member === (int) $m['ID'] ? 'selected' : '' ?>>
                                <?= e($m['Name']) ?> (ID: <?= e($m['ID']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Membership Plan:</label>
                    <select name="plan_id" class="form-control" onchange="updateAmount(this)" required>
                        <option value="">-- Select Plan --</option>
                        <?php while ($p = $plans->fetch_assoc()): ?>
                            <option value="<?= e($p['Plan_id']) ?>" data-amount="<?= e($p['Amount']) ?>" <?= $selected_plan === (int) $p['Plan_id'] ? 'selected' : '' ?>>
                                <?= e($p['Name']) ?> (৳<?= number_format((float) $p['Amount'], 2) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount Paid (৳):</label>
                    <input type="number" step="0.01" min="0.01" name="amount_paid" id="amount_paid" class="form-control"
                           value="<?= e($_POST['amount_paid'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Date of Purchase:</label>
                    <input type="date" name="date_of_purchase" class="form-control" max="<?= date('Y-m-d') ?>"
                           value="<?= e($_POST['date_of_purchase'] ?? date('Y-m-d')) ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Generate Receipt</button>
        </form>
    </div>

    <div class="card">
        <h3>Recent Receipts</h3>
        <div class="table-wrap">
            <table class="table">
                <tr><th>Receipt No</th><th>Member</th><th>Plan</th><th>Amount Paid</th><th>Purchased</th><th>Created</th><th>Issued By</th></tr>
                <?php if ($receipts->num_rows === 0): ?>
                    <tr><td colspan="7">No receipts yet.</td></tr>
                <?php endif; ?>
                <?php while ($r = $receipts->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($r['Receipt_no']) ?></td>
                        <td><?= e($r['Member_Name']) ?></td>
                        <td><?= e($r['Plan_Name']) ?></td>
                        <td>৳<?= number_format((float) $r['Amount_paid'], 2) ?></td>
                        <td><?= e($r['Date_of_purchase']) ?></td>
                        <td><?= e($r['Date_of_creation']) ?></td>
                        <td><?= e($r['Issued_By'] ?? '—') ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="navigation">
        <a href="receptionist.php" class="btn btn-primary">Back to Dashboard</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
