<?php
// public/index.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Equipment.php';
require_once __DIR__ . '/../classes/Loan.php';

$db = (new Database())->getConnection();
$message = '';
$error = '';

// ดึงรายชื่อผู้ใช้และอุปกรณ์
$usersStmt = $db->query("SELECT * FROM users");
$usersData = $usersStmt->fetchAll();

$eqStmt = $db->query("SELECT * FROM equipment");
$equipments = $eqStmt->fetchAll();

// จัดการการส่งฟอร์มยืมอุปกรณ์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'borrow') {
    try {
        $userId = (int)$_POST['user_id'];
        $equipmentId = (int)$_POST['equipment_id'];

        $userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $uData = $userStmt->fetch();

        if (!$uData) throw new Exception("ไม่พบข้อมูลผู้ใช้");

        $user = UserFactory::create($uData);
        $loanManager = new LoanManager($db);
        
        if ($loanManager->borrowEquipment($user, $equipmentId)) {
            $message = "ยืมอุปกรณ์เรียบร้อยแล้ว! กำหนดคืนภายใน {$user->getMaxBorrowDays()} วัน";
            // Refresh ข้อมูลอุปกรณ์
            $eqStmt = $db->query("SELECT * FROM equipment");
            $equipments = $eqStmt->fetchAll();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบยืม-คืนอุปกรณ์ห้องแล็บ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>ระบบยืม-คืนอุปกรณ์ห้องปฏิบัติการ</h1>
    <div class="nav">
        <a href="index.php">รายการอุปกรณ์ & ยืม</a> | 
        <a href="return.php">จัดการคืนอุปกรณ์ & ค่าปรับ</a>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <h2>ทำรายการยืมอุปกรณ์</h2>
    <form method="POST" style="background: #eef2f5; padding: 15px; border-radius: 5px;">
        <input type="hidden" name="action" value="borrow">
        <div class="form-group">
            <label>เลือกผู้ใช้งาน (เพื่อตรวจสอบสิทธิ์การยืม):</label>
            <select name="user_id" required>
                <?php foreach ($usersData as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>เลือกอุปกรณ์ที่ต้องการยืม:</label>
            <select name="equipment_id" required>
                <?php foreach ($equipments as $eq): ?>
                    <?php if ($eq['status'] === 'Available'): ?>
                        <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['code']) ?> - <?= htmlspecialchars($eq['name']) ?> (ค่าปรับล่วงเวลา: ฿<?= $eq['daily_penalty_rate'] ?>/วัน)</option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">ยืนยันการยืม</button>
    </form>

    <h2>รายการอุปกรณ์ทั้งหมดในระบบ</h2>
    <table>
        <thead>
            <tr>
                <th>รหัส</th>
                <th>ชื่ออุปกรณ์</th>
                <th>ประเภท</th>
                <th>ค่าปรับ/วัน</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $eq): ?>
            <tr>
                <td><?= htmlspecialchars($eq['code']) ?></td>
                <td><?= htmlspecialchars($eq['name']) ?></td>
                <td><?= htmlspecialchars($eq['type']) ?></td>
                <td>฿<?= number_format($eq['daily_penalty_rate'], 2) ?></td>
                <td>
                    <?php if ($eq['status'] === 'Available'): ?>
                        <span class="badge bg-success">พร้อมใช้งาน</span>
                    <?php else: ?>
                        <span class="badge bg-danger">ถูกยืมแล้ว</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
