<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRbacRolesAndAssignments extends Migration
{
    private const USER_ROLES = "ENUM('Admin','IT','Dispatch','Dispatcher','Accounting','HR','Marketing','Sales','Customer Service','Technician','Maintenance','Customer')";
    private const EMPLOYEE_DEPARTMENTS = "ENUM('Admin','IT','Dispatch','Dispatcher','Accounting','HR','Marketing','Sales','Customer Service','Technician','Maintenance')";

    public function up()
    {
        $this->db->query('ALTER TABLE users MODIFY role ' . self::USER_ROLES . ' NOT NULL');
        $this->db->query('ALTER TABLE employees MODIFY department ' . self::EMPLOYEE_DEPARTMENTS . ' NOT NULL');
        $this->db->query("ALTER TABLE users ADD approval_status VARCHAR(20) NOT NULL DEFAULT 'Approved' AFTER is_active");
        $this->db->query(
            "UPDATE users SET approval_status = 'Pending'
             WHERE is_active = 0 AND role <> 'Customer'"
        );
        $this->db->query('ALTER TABLE bookings ADD assigned_employee_id INT NULL');
        $this->db->query('CREATE INDEX idx_bookings_assigned_employee ON bookings (assigned_employee_id)');
        $this->db->query(
            'ALTER TABLE bookings ADD CONSTRAINT fk_bookings_assigned_employee
             FOREIGN KEY (assigned_employee_id) REFERENCES employees(id)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );
        $this->db->query(
            "ALTER TABLE quote_requests ADD status VARCHAR(30) NOT NULL DEFAULT 'Pending' AFTER privacy_consent"
        );
    }

    public function down()
    {
        $this->db->query("UPDATE users SET role = 'Technician' WHERE role = 'Maintenance'");
        $this->db->query("UPDATE employees SET department = 'Technician' WHERE department = 'Maintenance'");
        $this->db->query('ALTER TABLE quote_requests DROP COLUMN status');
        $this->db->query('ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_assigned_employee');
        $this->db->query('DROP INDEX idx_bookings_assigned_employee ON bookings');
        $this->db->query('ALTER TABLE bookings DROP COLUMN assigned_employee_id');
        $this->db->query('ALTER TABLE users DROP COLUMN approval_status');
        $this->db->query("ALTER TABLE users MODIFY role ENUM('Admin','IT','Dispatch','Dispatcher','Accounting','HR','Marketing','Sales','Customer Service','Technician','Customer') NOT NULL");
        $this->db->query("ALTER TABLE employees MODIFY department ENUM('Admin','IT','Dispatch','Dispatcher','Accounting','HR','Marketing','Sales','Customer Service','Technician','Customer') NOT NULL");
    }
}
