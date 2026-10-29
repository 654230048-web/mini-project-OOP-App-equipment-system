<?php
// classes/Equipment.php

abstract class Equipment {
    public function __construct(
        protected int $id,
        protected string $code,
        protected string $name,
        protected string $status,
        protected float $dailyPenaltyRate
    ) {}

    abstract public function getCategoryName(): string;

    public function getId(): int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function getName(): string { return $this->name; }
    public function getStatus(): string { return $this->status; }
    public function getDailyPenaltyRate(): float { return $this->dailyPenaltyRate; }
}

class ITEquipment extends Equipment {
    public function getCategoryName(): string { return 'อุปกรณ์ไอที / คอมพิวเตอร์'; }
}

class LabEquipment extends Equipment {
    public function getCategoryName(): string { return 'เครื่องมือห้องปฏิบัติการ'; }
}
