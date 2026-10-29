<?php
// public/return.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Equipment.php';
require_once __DIR__ . '/../classes/Loan.php';

$db = (new Database())->getConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return') {
    try {
        $loanId = (int)$_POST['loan_id'];
        $loanManager = new LoanManager($db);
        $result = $loanManager->returnEquipment($loanId);

        if ($result['success']) {
            $penalty = number_format($result['penalty'], 2);
            $message = "คืนอุปกรณ์สำเร็จ! ค่าปรับส่งคืนล่าช้า: ฿{$penalty}";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ดึงรายการที่กำลังถูกยืมอยู่
$stmt = $db->query("
    SELECT l.id AS loan_id, u.name AS user_name, u.role, e.name AS equipment_name, e.code, l.borrow_date, l.due_date, e.daily_penalty_rate
    FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN equipment e ON l.equipment_id = e.id
    WHERE l.status = 'Active'
");
$activeLoans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการคืนอุปกรณ์ - ระบบยืม-คืนอุปกรณ์</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>จัดการการคืนอุปกรณ์ & คำนวณค่าปรับ</h1>
    <div class="nav">
        <a href="index.php">รายการอุปกรณ์ & ยืม</a> | 
        <a href="return.php">จัดการคืนอุปกรณ์ & ค่าปรับ</a>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <h2>รายการที่อยู่ระหว่างการยืม</h2>
    <table>
        <thead>
            <tr>
                <th>ผู้ยืม (บทบาท)</th>
                <th>อุปกรณ์</th>
                <th>วันที่ยืม</th>
                <th>กำหนดคืน</th>
                <th>อัตราค่าปรับ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($activeLoans)): ?>
                <tr><td colspan="6" style="text-align:center;">ไม่มีรายการยืมในขณะนี้</td></tr>
            <?php else: ?>
                <?php foreach ($activeLoans as $loan): ?>
                <tr>
                    <td><?= htmlspecialchars($loan['user_name']) ?> (<?= htmlspecialchars($loan['role']) ?>)</td>
                    <td><?= htmlspecialchars($loan['code']) ?> - <?= htmlspecialchars($loan['equipment_name']) ?></td>
                    <td><?= htmlspecialchars($loan['borrow_date']) ?></td>
                    <td>
                        <?= htmlspecialchars($loan['due_date']) ?>
                        <?php if (date('Y-m-d') > $loan['due_date']): ?>
                            <span class="badge bg-danger">เลยกำหนด</span>
                        <?php endif; ?>
                    </td>
                    <td>฿<?= number_format($loan['daily_penalty_rate'], 2) ?>/วัน</td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                            <button type="submit" class="btn btn-success">บันทึกรับคืน</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
