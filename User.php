<?php
// classes/User.php

abstract class User {
    public function __construct(
        protected int $id,
        protected string $name,
        protected string $email,
        protected string $role
    ) {}

    abstract public function getMaxBorrowDays(): int;
    abstract public function getMaxItemsAllowed(): int;

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getRole(): string { return $this->role; }
}

class StudentUser extends User {
    public function getMaxBorrowDays(): int { return 7; } // ยืมได้สูงสุด 7 วัน
    public function getMaxItemsAllowed(): int { return 2; } // ยืมได้สูงสุด 2 ชิ้น
}

class TeacherUser extends User {
    public function getMaxBorrowDays(): int { return 30; } // ยืมได้สูงสุด 30 วัน
    public function getMaxItemsAllowed(): int { return 5; } // ยืมได้สูงสุด 5 ชิ้น
}

class AdminUser extends User {
    public function getMaxBorrowDays(): int { return 90; }
    public function getMaxItemsAllowed(): int { return 10; }
}

class UserFactory {
    public static function create(array $data): User {
        return match ($data['role']) {
            'Student' => new StudentUser($data['id'], $data['name'], $data['email'], 'Student'),
            'Teacher' => new TeacherUser($data['id'], $data['name'], $data['email'], 'Teacher'),
            'Admin'   => new AdminUser($data['id'], $data['name'], $data['email'], 'Admin'),
            default   => throw new Exception("Invalid User Role"),
        };
    }
}
