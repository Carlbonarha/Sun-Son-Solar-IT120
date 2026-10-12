<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuoteRequests extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'customer_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email_address' => ['type' => 'VARCHAR', 'constraint' => 255],
            'phone_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'service_needed' => ['type' => 'VARCHAR', 'constraint' => 255],
            'property_address' => ['type' => 'TEXT'],
            'property_type' => ['type' => 'VARCHAR', 'constraint' => 255],
            'average_monthly_electricity_bill' => ['type' => 'VARCHAR', 'constraint' => 100],
            'preferred_contact_method' => ['type' => 'VARCHAR', 'constraint' => 100],
            'additional_details' => ['type' => 'TEXT', 'null' => true],
            'preferred_date_or_schedule' => ['type' => 'VARCHAR', 'constraint' => 255],
            'attachment_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'privacy_consent' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('customer_id');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('quote_requests', true);
    }

    public function down()
    {
        $this->forge->dropTable('quote_requests', true);
    }
}
