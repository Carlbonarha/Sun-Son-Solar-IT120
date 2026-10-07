<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartmentsAndSharedCatalog extends Migration
{
    public function up()
    {
        $roles = "ENUM('Admin','IT','Dispatch','Dispatcher','Accounting','HR','Marketing','Sales','Customer Service','Technician','Customer')";
        $this->db->query("ALTER TABLE users MODIFY role {$roles} NOT NULL");
        $this->db->query("ALTER TABLE employees MODIFY department {$roles} NOT NULL");

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'category' => ['type' => 'VARCHAR', 'constraint' => 120],
            'price' => ['type' => 'VARCHAR', 'constraint' => 80],
            'description' => ['type' => 'TEXT'],
            'photo' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('catalog_products', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'service_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT'],
            'photo' => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('catalog_services', true);

        $now = date('Y-m-d H:i:s');
        $this->db->table('catalog_products')->insertBatch([
            ['name' => 'High-Efficiency Solar Panels', 'category' => 'Panels', 'price' => '$350', 'description' => 'High-efficiency solar panels with 25-year warranty', 'photo' => null, 'created_at' => $now],
            ['name' => 'Solar Inverters', 'category' => 'Inverters', 'price' => '$2,000', 'description' => 'Convert DC to AC power efficiently', 'photo' => null, 'created_at' => $now],
            ['name' => 'Energy Storage Batteries', 'category' => 'Batteries', 'price' => '$5,000', 'description' => 'Lithium energy storage solutions for backup power', 'photo' => null, 'created_at' => $now],
            ['name' => 'Racking Systems', 'category' => 'Racking and Mounting', 'price' => '$1,200', 'description' => 'Durable mounting systems for any roof type', 'photo' => null, 'created_at' => $now],
            ['name' => 'Solar Wiring Kit', 'category' => 'Wires', 'price' => '$300', 'description' => 'Safe and certified solar electrical wiring', 'photo' => null, 'created_at' => $now],
        ]);
        $this->db->table('catalog_services')->insertBatch([
            ['name' => 'Free Consultation', 'service_type' => 'Consultation', 'description' => 'Free initial consultation to assess your energy needs', 'photo' => null, 'created_at' => $now],
            ['name' => 'System Design', 'service_type' => 'Designing', 'description' => 'Custom solar system design by our engineers', 'photo' => null, 'created_at' => $now],
            ['name' => 'Permit Handling', 'service_type' => 'Permitting', 'description' => 'Handle all necessary permits and documentation', 'photo' => null, 'created_at' => $now],
            ['name' => 'Professional Installation', 'service_type' => 'Installation', 'description' => 'Professional installation by certified technicians', 'photo' => null, 'created_at' => $now],
            ['name' => 'Regular Maintenance', 'service_type' => 'Maintenance', 'description' => 'Regular system maintenance and cleaning', 'photo' => null, 'created_at' => $now],
            ['name' => 'Quick Repairs', 'service_type' => 'Repair', 'description' => 'Quick repair services for system issues', 'photo' => null, 'created_at' => $now],
            ['name' => '24/7 Monitoring', 'service_type' => 'Monitoring', 'description' => '24/7 system performance monitoring', 'photo' => null, 'created_at' => $now],
        ]);

        $this->db->query(
            "UPDATE employees e JOIN users u ON u.id = e.user_id
             SET e.department = 'Dispatch' WHERE u.role = 'Dispatcher'"
        );
    }

    public function down()
    {
        $this->forge->dropTable('catalog_services', true);
        $this->forge->dropTable('catalog_products', true);
        $legacyRoles = "ENUM('Admin','Technician','Dispatcher','Customer')";
        $this->db->query("UPDATE users SET role = 'Dispatcher' WHERE role = 'Dispatch'");
        $this->db->query("ALTER TABLE users MODIFY role {$legacyRoles} NOT NULL");
        $this->db->query("UPDATE employees SET department = 'Dispatcher' WHERE department = 'Dispatch'");
        $this->db->query("ALTER TABLE employees MODIFY department ENUM('Admin','Technician','Dispatcher') NOT NULL");
    }
}
