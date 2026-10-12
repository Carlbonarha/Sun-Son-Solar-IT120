<?= view('departments/department-workspace', ['department' => [
    'role' => 'Customer Service',
    'dashboard' => 'customer-service-dashboard.php',
    'description' => 'Review customer inquiries, quote requests, service requests, and scheduling details to assist customers.',
    'modules' => [
        ['id' => 'serviceRequests', 'title' => 'Customer requests', 'description' => 'Requests currently recorded in the shared booking system.', 'metric' => 'departmentPrimaryMetric', 'size' => 'span-2'],
        ['id' => 'servicePending', 'title' => 'Awaiting operations', 'description' => 'Requests still marked pending.', 'metric' => 'departmentSecondaryMetric', 'size' => 'span-2'],
        ['id' => 'bookingQueue', 'title' => 'Customer support queue', 'description' => 'Search request details, help customers, and update service request status.', 'size' => 'span-4'],
        ['id' => 'quoteQueue', 'title' => 'Customer inquiries and quotes', 'description' => 'Review quote inquiries and update customer follow-up status.', 'size' => 'span-4'],
    ],
]]) ?>
