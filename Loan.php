<?php
// classes/Loan.php

require_once __DIR__ . '/PenaltyCalculator.php';

class LoanManager {
    public function __construct(private PDO $db) {}

    public function borrowEquipment(User $user, int $equipmentId): bool {
        // 1. ตรวจสอบสถานะอุปกรณ์
        $stmt = $this->db->prepare("SELECT * FROM equipment WHERE id = ? AND status = 'Available'");
        $stmt->execute([$equipmentId]);
        $eq = $stmt->fetch();

        if (!$eq) {
            throw new Exception("อุปกรณ์ไม่พร้อมใช้งาน หรือถูกยืมไปแล้ว");
        }

        // 2. คำนวณวันกำหนดคืนตามสิทธิ์ของผู้ใช้
        $borrowDate = date('Y-m-d');
        $maxDays = $user->getMaxBorrowDays();
        $dueDate = date('Y-m-d', strtotime("+{$maxDays} days"));

        $this->db->beginTransaction();
        try {
            // บันทึกการยืม
            $stmt = $this->db->prepare("INSERT INTO loans (user_id, equipment_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'Active')");
            $stmt->execute([$user->getId(), $equipmentId, $borrowDate, $dueDate]);

            // อัปเดตสถานะอุปกรณ์
            $stmt = $this->db->prepare("UPDATE equipment SET status = 'Borrowed' WHERE id = ?");
            $stmt->execute([$equipmentId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function returnEquipment(int $loanId): array {
        $stmt = $this->db->prepare("
            SELECT l.*, e.daily_penalty_rate, e.id AS equipment_id 
            FROM loans l 
            JOIN equipment e ON l.equipment_id = e.id 
            WHERE l.id = ? AND l.status = 'Active'
        ");
        $stmt->execute([$loanId]);
        $loan = $stmt->fetch();

        if (!$loan) {
            throw new Exception("ไม่พบรายการยืมที่รอดำเนินการคืน");
        }

        $returnDate = date('Y-m-d');
        $penalty = PenaltyCalculator::calculate($loan['due_date'], $returnDate, (float)$loan['daily_penalty_rate']);

        $this->db->beginTransaction();
        try {
            // อัปเดตรายการยืม
            $stmt = $this->db->prepare("UPDATE loans SET return_date = ?, penalty_fee = ?, status = 'Returned' WHERE id = ?");
            $stmt->execute([$returnDate, $penalty, $loanId]);

            // คืนสถานะอุปกรณ์ให้พร้อมใช้งาน
            $stmt = $this->db->prepare("UPDATE equipment SET status = 'Available' WHERE id = ?");
            $stmt->execute([$loan['equipment_id']]);

            $this->db->commit();

            return [
                'success' => true,
                'penalty' => $penalty,
                'return_date' => $returnDate
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
