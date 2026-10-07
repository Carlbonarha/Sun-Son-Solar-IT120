<?= view('departments/department-workspace', ['department' => [
    'role' => 'HR',
    'dashboard' => 'hr-dashboard.php',
    'description' => 'Maintain an employee directory view and review recorded attendance by month. User credentials and account-role changes remain Admin-only.',
    'modules' => [
        ['id' => 'hrTools', 'title' => 'Employee directory & attendance', 'description' => 'Employee records and recorded check-ins for the selected month.', 'size' => 'span-4'],
    ],
]]) ?>
