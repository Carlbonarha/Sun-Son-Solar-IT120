<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillEmployeeProfiles extends Migration
{
    public function up()
    {
        $this->db->query(
            "INSERT INTO employees (user_id, first_name, last_name, department)
             SELECT u.id, u.username, 'User', u.role
             FROM users u
             LEFT JOIN employees e ON e.user_id = u.id
             WHERE u.role IN ('Admin', 'Technician', 'Dispatcher')
               AND e.id IS NULL"
        );
    }

    public function down()
    {
    }
}
