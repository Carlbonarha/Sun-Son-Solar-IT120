<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequestRoutingAndCustomerReviews extends Migration
{
    public function up()
    {
        $this->forge->addColumn('quote_requests', [
            'assigned_department' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'status'],
            'assigned_employee_id' => ['type' => 'INT', 'null' => true, 'after' => 'assigned_department'],
        ]);
        $this->db->query('CREATE INDEX idx_quote_requests_assigned_employee ON quote_requests (assigned_employee_id)');
        $this->db->query(
            'ALTER TABLE quote_requests ADD CONSTRAINT fk_quote_requests_assigned_employee
             FOREIGN KEY (assigned_employee_id) REFERENCES employees(id)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'request_type' => ['type' => 'VARCHAR', 'constraint' => 20],
            'request_id' => ['type' => 'INT', 'constraint' => 11],
            'sender_user_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'sender_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'message' => ['type' => 'TEXT'],
            'is_internal' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['request_type', 'request_id', 'created_at'], false, false, 'idx_request_messages_thread');
        $this->forge->addKey('sender_user_id');
        $this->forge->createTable('request_messages', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'customer_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'service_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'story' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pending'],
            'privacy_consent' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
            'published_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('published_at');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('customer_reviews', true);
    }

    public function down()
    {
        $this->forge->dropTable('customer_reviews', true);
        $this->forge->dropTable('request_messages', true);
        $this->db->query('ALTER TABLE quote_requests DROP FOREIGN KEY fk_quote_requests_assigned_employee');
        $this->db->query('DROP INDEX idx_quote_requests_assigned_employee ON quote_requests');
        $this->forge->dropColumn('quote_requests', ['assigned_department', 'assigned_employee_id']);
    }
}
