<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;
use Throwable;

class InitialUsers extends Migration
{
    private const KATHERINE = [
        'username'      => 'KittyKat16',
        'password'      => '$2y$10$RGEPV6hKx8KOrtAm0u.v1eZVazIgyT22G.hNUAg4iwR9x54eIE/0u',
        'legacyUsername' => 'Kitty Kat16',
        'first_name'    => 'Katherine',
        'last_name'     => 'sinagaraw',
        'birthdate'     => '1990-07-01',
        'email'         => 'Katherine.sinagaraw@sunsonsolar.com',
        'phone_number'  => '09291230983',
    ];

    private const SOL = [
        'username'       => 'Admin',
        'password'       => '$2y$10$jvpYSu7YutPJPdeCrUz.yOf9lq/o66hf9n87lEntVMdgFud8yrwNm',
        'legacyUsername' => 'sol.solis',
        'first_name'     => 'sol',
        'last_name'      => 'solis',
        'birthdate'      => '1967-01-08',
        'email'          => 'sol.solis@sunsonsolar.com',
    ];

    public function up()
    {
        $this->db->transBegin();

        try {
            $this->provisionKatherine();
            $this->provisionSol();

            if ($this->db->transStatus() === false) {
                throw new RuntimeException('Unable to provision the initial admin accounts.');
            }

            $this->db->transCommit();
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        }
    }

    public function down()
    {
    }

    private function provisionKatherine(): void
    {
        $account = $this->findUser(self::KATHERINE['username'])
            ?? $this->findUser(self::KATHERINE['legacyUsername']);

        $userId = $this->saveAdminUser($account, self::KATHERINE);
        $profile = $this->findEmployee($userId);

        $data = [
            'first_name'   => self::KATHERINE['first_name'],
            'last_name'    => self::KATHERINE['last_name'],
            'birthdate'    => self::KATHERINE['birthdate'],
            'email'        => self::KATHERINE['email'],
            'phone_number' => self::KATHERINE['phone_number'],
            'department'   => 'Admin',
        ];

        if ($profile === null) {
            $this->db->table('employees')->insert($data + [
                'user_id'     => $userId,
                'middle_name' => null,
                'gender'      => null,
                'address'     => null,
            ]);

            return;
        }

        $this->db->table('employees')->where('user_id', $userId)->update($data);
    }

    private function provisionSol(): void
    {
        $profile = $this->db->table('employees')
            ->where('email', self::SOL['email'])
            ->get()
            ->getRowArray();
        $account = $profile === null
            ? null
            : $this->findUserById((int) $profile['user_id']);

        $account ??= $this->findUser(self::SOL['legacyUsername']);

        $usernameConflict = $this->findUser(self::SOL['username']);
        if ($usernameConflict !== null && ($account === null || (int) $usernameConflict['id'] !== (int) $account['id'])) {
            $legacyName = 'admin-legacy';
            if ($this->findUser($legacyName) !== null) {
                throw new RuntimeException('Cannot preserve the existing admin account because admin-legacy is already in use.');
            }

            $this->db->table('users')
                ->where('id', $usernameConflict['id'])
                ->update(['username' => $legacyName]);
        }

        $userId = $this->saveAdminUser($account, self::SOL);
        $profile = $this->findEmployee($userId);

        $data = [
            'first_name' => self::SOL['first_name'],
            'last_name'  => self::SOL['last_name'],
            'birthdate'  => self::SOL['birthdate'],
            'email'      => self::SOL['email'],
            'department' => 'Admin',
        ];

        if ($profile === null) {
            $this->db->table('employees')->insert($data + [
                'user_id'      => $userId,
                'middle_name'  => null,
                'gender'       => null,
                'phone_number' => null,
                'address'      => null,
            ]);

            return;
        }

        $this->db->table('employees')->where('user_id', $userId)->update($data);
    }

    private function saveAdminUser(?array $account, array $details): int
    {
        $data = [
            'username' => $details['username'],
            'password' => $details['password'],
            'role'     => 'Admin',
        ];

        if ($account === null) {
            $this->db->table('users')->insert($data);

            return (int) $this->db->insertID();
        }

        $this->db->table('users')->where('id', $account['id'])->update($data);

        return (int) $account['id'];
    }

    private function findUser(string $username): ?array
    {
        return $this->db->table('users')
            ->where('username', $username)
            ->get()
            ->getRowArray();
    }

    private function findUserById(int $userId): ?array
    {
        if ($userId < 1) {
            return null;
        }

        return $this->db->table('users')
            ->where('id', $userId)
            ->get()
            ->getRowArray();
    }

    private function findEmployee(int $userId): ?array
    {
        return $this->db->table('employees')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();
    }
}