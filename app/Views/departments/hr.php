<?= view('departments/department-workspace', ['department' => [
    'role' => 'HR',
    'dashboard' => 'hr-dashboard.php',
    'description' => 'Create employee accounts, review employee records, and review recorded attendance by month.',
    'modules' => [
        ['id' => 'hrCreateEmployee', 'title' => 'Create employee account', 'description' => 'Create an employee account and assign the appropriate department.', 'size' => 'span-4'],
        ['id' => 'hrTools', 'title' => 'Employee directory & attendance', 'description' => 'Employee records and recorded check-ins for the selected month.', 'size' => 'span-4'],
    ],
]]) ?>
