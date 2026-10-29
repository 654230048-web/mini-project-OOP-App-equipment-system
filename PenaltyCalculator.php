<?php
// classes/PenaltyCalculator.php

class PenaltyCalculator {
    public static function calculate(string $dueDateStr, string $returnDateStr, float $dailyRate): float {
        $dueDate = new DateTime($dueDateStr);
        $returnDate = new DateTime($returnDateStr);

        // ถ้าคืนก่อนหรือตรงกำหนด ไม่มีค่าปรับ
        if ($returnDate <= $dueDate) {
            return 0.0;
        }

        $overdueDays = $returnDate->diff($dueDate)->days;
        return $overdueDays * $dailyRate;
    }
}
