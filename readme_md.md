# 🛠️ Lab Equipment & Asset Rental System
> **Mini Project OOAD & OOP (PHP 8)**  
> ระบบบริหารจัดการยืม-คืนอุปกรณ์ห้องปฏิบัติการและอุปกรณ์ไอที พร้อมระบบคำนวณค่าปรับอัตโนมัติ

---

## 📌 คุณสมบัติของระบบ (Features)

* **ระบบการยืมอุปกรณ์ (Equipment Borrowing):**
  * ตรวจสอบสถานะความพร้อมของอุปกรณ์ก่อนทำรายการยืม
  * ตรวจสอบสิทธิ์และสิทธิประโยชน์ของผู้ใช้งานตามบทบาท (Role) อัตโนมัติ
* **ระบบการคืนอุปกรณ์และคำนวณค่าปรับ (Equipment Return & Penalty Calculation):**
  * คำนวณค่าปรับล่วงเวลา (Overdue Fee) อัตโนมัติเมื่อส่งคืนเกินกำหนด
  * อัปเดตสถานะอุปกรณ์กลับมาเป็น "พร้อมใช้งาน" อัตโนมัติหลังคืนสำเร็จ
* **การจัดการสิทธิ์การใช้งาน (Role-Based Authorization):**
  * **นักศึกษา (Student):** ยืมได้สูงสุด 7 วัน
  * **อาจารย์ (Teacher):** ยืมได้สูงสุด 30 วัน
  * **ผู้ดูแลระบบ (Admin):** ยืมได้สูงสุด 90 วัน

---

## 🧬 โครงสร้างการออกแบบสถาปัตยกรรม (OOAD & OOP Principles)

โปรเจกต์นี้ออกแบบตามหลักการ **Object-Oriented Analysis and Design (OOAD)** และ **SOLID Principles**:

1. **Abstraction & Inheritance:**
   * `User` (Abstract Class) ➔ แยกสืบทอดเป็น `StudentUser`, `TeacherUser`, `AdminUser`
   * `Equipment` (Abstract Class) ➔ แยกสืบทอดเป็น `ITEquipment`, `LabEquipment`
2. **Polymorphism:**
   * การคำนวณระยะเวลาการยืมสูงสุด (`getMaxBorrowDays()`) ทำงานแตกต่างกันไปตาม Class ของผู้ใช้งานแต่ละบทบาท
3. **Design Patterns Used:**
   * **Factory Pattern (`UserFactory`):** ใช้จัดการการสร้าง Object ผู้ใช้งานตาม Role ที่ดึงมาจากฐานข้อมูล
4. **Single Responsibility Principle (SRP):**
   * `PenaltyCalculator`: รับหน้าที่คำนวณค่าปรับอย่างเดียวโดยไม่ยุ่งเกี่ยวกับคลาสอื่น
   * `LoanManager`: ควบคุม Business Logic การยืมและคืนอุปกรณ์

---

## 📁 โครงสร้างโฟลเดอร์ (Directory Structure)

```text
equipment-system/
├── config/
│   └── database.php       # ไฟล์ตั้งค่าเชื่อมต่อฐานข้อมูล PDO
├── classes/
│   ├── User.php           # User, Student, Teacher, Admin (Inheritance & Factory Pattern)
│   ├── Equipment.php      # Equipment (Abstract), ITEquipment, LabEquipment
│   ├── Loan.php           # รายการยืม-คืน & Business Logic
│   └── PenaltyCalculator.php # คลาสคำนวณค่าปรับส่งคืนล่าช้า
├── public/
│   ├── index.php          # หน้าหลัก / แสดงรายการอุปกรณ์ & ฟอร์มยืม
│   ├── return.php         # หน้าจัดการคืนอุปกรณ์ & คำนวณค่าปรับ
│   └── style.css          # ไฟล์ CSS ตกแต่ง UI
├── index.php              # ไฟล์ Redirect ไปยัง public/index.php
└── schema.sql             # SQL Script สำหรับสร้างตารางและข้อมูลเริ่มต้น
```

---

## 🚀 ขั้นตอนการติดตั้งและการใช้งาน (Installation)

### 1. การตั้งค่าฐานข้อมูล (Database Setup)
1. สร้างฐานข้อมูล MySQL ชื่อ `equipment_db` (หรือชื่อฐานข้อมูลบน Hosting)
2. นำไฟล์ `schema.sql` ไป Import ใน **phpMyAdmin**

### 2. การตั้งค่าการเชื่อมต่อ (Configuration)
เปิดไฟล์ `config/database.php` และปรับเปลี่ยนค่าการเชื่อมต่อฐานข้อมูลตามสภาพแวดล้อมของคุณ:

```php
private string $host = 'localhost';
private string $db_name = 'equipment_db';
private string $username = 'root';
private string $password = '';
```

### 3. การเข้าใช้งานผ่าน Web Browser
* **การรันบน XAMPP / Localhost:**  
  `http://localhost/equipment-system/public/index.php`
* **การรันบน Web Hosting (InfinityFree):**  
  `http://your-domain.free.nf/` (ระบบจะ Redirect เข้าสู่ `public/index.php` อัตโนมัติ)

---

## 🛠️ เทคโนโลยีที่ใช้ (Tech Stack)

* **Language:** PHP 8.1+
* **Database:** MySQL / MariaDB (PDO Driver)
* **Frontend:** HTML5, CSS3