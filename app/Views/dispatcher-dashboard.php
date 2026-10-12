<?php
$showBookings = $showBookings ?? false;
$canManageCatalog = $canManageCatalog ?? false;
$dashboard = $dashboard ?? 'dispatcher-dashboard.php';
$workspaceName = $showBookings
    ? (session()->get('role') === 'Admin' ? 'Administration' : 'Operations')
    : 'Marketing';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($workspaceName) ?> Workspace - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        body { background: #f3f7fb; }
        .workspace { display: flex; min-height: calc(100vh - 68px); }
        .side-panel { width: 220px; background: #123b6d; padding: 24px 14px; }
        .side-panel h2 { color: white; font-size: 18px; padding: 0 12px 20px; }
        .side-panel button { width: 100%; border: 0; background: transparent; color: #dbe9f7; text-align: left; padding: 12px; border-radius: 6px; cursor: pointer; font: inherit; }
        .side-panel button:hover, .side-panel button.active { background: #1a73e8; color: white; }
        .workspace-content { flex: 1; padding: 28px; min-width: 0; }
        .workspace-view { display: none; }
        .workspace-view.active { display: block; }
        .workspace-view h1 { color: #123b6d; margin-bottom: 8px; }
        .workspace-view > p { color: #666; margin-bottom: 20px; }
        .management-grid { display: grid; grid-template-columns: minmax(260px, 340px) 1fr; gap: 20px; align-items: start; }
        .panel { background: white; border-radius: 8px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .panel h2 { color: #1a73e8; font-size: 19px; margin-bottom: 16px; }
        .data-list { display: grid; gap: 12px; }
        .data-item { background: white; border: 1px solid #e1e5e9; border-left: 4px solid #1a73e8; border-radius: 6px; padding: 14px; }
        .data-item h3 { color: #1a73e8; font-size: 16px; margin-bottom: 6px; }
        .data-item p { color: #666; font-size: 14px; margin: 4px 0; }
        .data-actions { display: flex; gap: 8px; margin-top: 10px; }
        .data-actions button { flex: 1; }
        .btn-small { padding: 8px; border: 0; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-edit { background: #e3f0ff; color: #1557b0; }
        .btn-delete { background: #f8d7da; color: #721c24; }
        .item-photo { width: 100%; height: 130px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
        .photo-placeholder { width: 100%; height: 130px; display: grid; place-items: center; background: linear-gradient(135deg, #eef5fa, #dce9f4); color: #56718b; border-radius: 5px; margin-bottom: 10px; font-weight: 700; }
        .status-select { padding: 6px; border: 1px solid #ddd; border-radius: 4px; }
        .empty-state { color: #666; padding: 20px 0; }
        @media (max-width: 800px) { .workspace { display: block; } .side-panel { width: 100%; display: flex; overflow-x: auto; gap: 6px; padding: 10px; } .side-panel h2 { display: none; } .side-panel button { white-space: nowrap; width: auto; } .workspace-content { padding: 18px 12px; } .management-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar · <?= esc($workspaceName) ?></div>
        <nav class="header-nav" aria-label="Dispatcher navigation">
            <span id="dispatcherUsername"></span>
            <a href="<?= site_url($dashboard) ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('employee-timein.php') ?>">Time In</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="workspace">
        <aside class="side-panel" aria-label="Dispatcher sections">
            <h2><?= esc($workspaceName) ?></h2>
            <?php if ($showBookings): ?>
                <button class="active" data-view="bookingsView">Bookings</button>
                <button data-view="quotesView">Quote requests</button>
            <?php endif; ?>
            <?php if ($canManageCatalog): ?>
                <button class="<?= $showBookings ? '' : 'active' ?>" data-view="servicesView">Services</button>
                <button data-view="productsView">Products</button>
            <?php endif; ?>
        </aside>

        <section class="workspace-content">
            <div id="dispatcherAlert" class="alert" role="alert" aria-live="polite"></div>
            <?php if ($showBookings): ?>
            <section class="workspace-view active" id="bookingsView">
                <h1>Customer Bookings</h1>
                <p>Review service requests, assign jobs to the correct team, and track operational status.</p>
                <div class="data-list" id="bookingsList"></div>
            </section>
            <section class="workspace-view" id="quotesView">
                <h1>Free Quote Requests</h1>
                <p>Review quote requests and customer contact information for operational follow-up.</p>
                <div class="data-list" id="quoteRequestsList">Loading quote requests…</div>
            </section>
            <?php endif; ?>

            <?php if ($canManageCatalog): ?>
            <section class="workspace-view <?= $showBookings ? '' : 'active' ?>" id="servicesView">
                <h1>Manage Services</h1>
                <p>Create, update, or remove the services shown to customers.</p>
                <div class="management-grid">
                    <form class="panel" id="serviceForm">
                        <h2 id="serviceFormTitle">Add Service</h2>
                        <input id="serviceId" type="hidden">
                        <div class="form-group"><label for="serviceName">Name *</label><input id="serviceName" required></div>
                        <div class="form-group"><label for="serviceType">Type *</label><select id="serviceType" required><option value="Consultation">Consultation</option><option value="Designing">Designing</option><option value="Permitting">Permitting</option><option value="Installation">Installation</option><option value="Maintenance">Maintenance</option><option value="Repair">Repair</option><option value="Monitoring">Monitoring</option></select></div>
                        <div class="form-group"><label for="serviceDescription">Description *</label><textarea id="serviceDescription" required></textarea></div>
                        <div class="form-group"><label for="servicePhoto">Photo</label><input id="servicePhoto" type="file" accept="image/*"></div>
                        <button class="btn btn-primary" type="submit">Save Service</button>
                        <button class="btn btn-secondary" type="button" onclick="resetServiceForm()">Clear</button>
                    </form>
                    <div class="data-list" id="servicesList"></div>
                </div>
            </section>

            <section class="workspace-view" id="productsView">
                <h1>Manage Products</h1>
                <p>Create, update, or remove products and attach product photos.</p>
                <div class="management-grid">
                    <form class="panel" id="productForm">
                        <h2 id="productFormTitle">Add Product</h2>
                        <input id="productId" type="hidden">
                        <div class="form-group"><label for="productName">Name *</label><input id="productName" required></div>
                        <div class="form-group"><label for="productCategory">Category *</label><select id="productCategory" required><option>Panels</option><option>Inverters</option><option>Batteries</option><option>Racking and Mounting</option><option>Wires</option></select></div>
                        <div class="form-group"><label for="productPrice">Price *</label><input id="productPrice" required placeholder="$350"></div>
                        <div class="form-group"><label for="productDescription">Description *</label><textarea id="productDescription" required></textarea></div>
                        <div class="form-group"><label for="productPhoto">Photo</label><input id="productPhoto" type="file" accept="image/*"></div>
                        <button class="btn btn-primary" type="submit">Save Product</button>
                        <button class="btn btn-secondary" type="button" onclick="resetProductForm()">Clear</button>
                    </form>
                    <div class="data-list" id="productsList"></div>
                </div>
            </section>
            <?php endif; ?>
        </section>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const dispatcher = { id: <?= (int) session()->get('userId') ?>, username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, role: <?= json_encode((string) session()->get('role'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?> };
        const showBookings = <?= $showBookings ? 'true' : 'false' ?>;
        localStorage.setItem('currentUser', JSON.stringify(dispatcher));
        document.getElementById('dispatcherUsername').textContent = `@${dispatcher.username}`;

        const escapeHtml = value => String(value || '').replace(/[&<>'"]/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[character]));
        const readPhoto = file => file ? new Promise(resolve => { const reader = new FileReader(); reader.onload = () => resolve(reader.result); reader.readAsDataURL(file); }) : Promise.resolve('');
        async function requestCatalog(path, method = 'GET', fields = {}) {
            const response = await fetch('<?= rtrim(site_url(''), '/') . '/' ?>' + path, {
                method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(method === 'POST' ? { 'Content-Type': 'application/x-www-form-urlencoded' } : {})
                },
                body: method === 'POST' ? new URLSearchParams(fields) : undefined
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Catalog request failed.');
            return result;
        }

        document.querySelectorAll('.side-panel button').forEach(button => button.addEventListener('click', () => {
            document.querySelectorAll('.side-panel button').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.workspace-view').forEach(view => view.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(button.dataset.view).classList.add('active');
        }));

        async function renderBookings() {
            const container = document.getElementById('bookingsList');
            container.textContent = 'Loading bookings…';
            const [response, assignableResponse] = await Promise.all([
                fetch('<?= site_url('bookings/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                fetch('<?= site_url('bookings/assignable-employees') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            ]);
            const result = await response.json();
            const assignableResult = await assignableResponse.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load bookings.');
            if (!assignableResponse.ok || !assignableResult.success) throw new Error(assignableResult.error || 'Could not load assignable employees.');
            const bookings = result.bookings;
            const employees = assignableResult.employees;
            container.innerHTML = bookings.length ? '' : '<p class="empty-state">No customer bookings yet.</p>';
            bookings.forEach(booking => {
                const item = document.createElement('article');
                item.className = 'data-item';
                item.innerHTML = `<h3>${escapeHtml(booking.serviceType)} - ${escapeHtml(booking.customerName)}</h3><p><strong>When:</strong> ${escapeHtml(booking.date)} at ${escapeHtml(booking.time)} (${escapeHtml(booking.duration)})</p><p><strong>Contact:</strong> ${escapeHtml(booking.email)} | ${escapeHtml(booking.phone)}</p><p><strong>Address:</strong> ${escapeHtml(booking.address)}</p><p><strong>Notes:</strong> ${escapeHtml(booking.notes) || 'None'}</p><div class="data-actions"><select class="status-select" data-booking-id="${booking.id}"><option ${booking.status === 'Pending' ? 'selected' : ''}>Pending</option><option ${booking.status === 'Confirmed' ? 'selected' : ''}>Confirmed</option><option ${booking.status === 'Completed' ? 'selected' : ''}>Completed</option><option ${booking.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option></select></div>`;
                const statusSelect = item.querySelector('.status-select');
                statusSelect.addEventListener('change', async event => {
                    try {
                        const response = await fetch('<?= site_url('bookings/status') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ id: booking.id, status: event.target.value })
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) throw new Error(result.error || 'Could not update booking.');
                        updateBookingStatus(booking.id, event.target.value);
                    } catch (error) {
                        showAlert('dispatcherAlert', error.message, 'error');
                        renderBookings().catch(loadError => showAlert('dispatcherAlert', loadError.message, 'error'));
                    }
                });
                const assignSelect = document.createElement('select');
                assignSelect.className = 'status-select';
                assignSelect.setAttribute('aria-label', `Assign ${booking.serviceType} job`);
                const unassigned = document.createElement('option');
                unassigned.value = '';
                unassigned.textContent = 'Unassigned';
                assignSelect.appendChild(unassigned);
                const employeesForBooking = employees.filter(employee => {
                    if (booking.serviceType === 'Maintenance') return employee.role === 'Maintenance';
                    return ['Designing', 'Installation', 'Repair', 'Monitoring'].includes(booking.serviceType)
                        && employee.role === 'Technician';
                });
                employeesForBooking.forEach(employee => {
                    const option = document.createElement('option');
                    option.value = String(employee.employeeId);
                    option.textContent = `${employee.firstName} ${employee.lastName} · ${employee.role}`;
                    option.selected = Number(booking.assignedEmployeeId) === Number(employee.employeeId);
                    assignSelect.appendChild(option);
                });
                assignSelect.addEventListener('change', async () => {
                    assignSelect.disabled = true;
                    try {
                        const response = await fetch('<?= site_url('bookings/assign') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ id: booking.id, employeeId: assignSelect.value })
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) throw new Error(result.error || 'Could not assign job.');
                        booking.assignedEmployeeId = result.employeeId;
                        showAlert('dispatcherAlert', 'Job assigned to the selected team member.', 'success');
                    } catch (error) {
                        showAlert('dispatcherAlert', error.message, 'error');
                        await renderBookings();
                    } finally {
                        assignSelect.disabled = false;
                    }
                });
                item.querySelector('.data-actions').appendChild(assignSelect);
                container.appendChild(item);
            });
        }

        async function renderQuoteRequests() {
            const container = document.getElementById('quoteRequestsList');
            const response = await fetch('<?= site_url('quote-requests/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load quote requests.');
            container.replaceChildren();
            if (!result.requests.length) {
                container.textContent = 'No free quote requests yet.';
                return;
            }
            result.requests.forEach(request => {
                const item = document.createElement('article');
                item.className = 'data-item';
                const title = document.createElement('h3');
                title.textContent = `${request.fullName} · ${request.serviceNeeded}`;
                const contact = document.createElement('p');
                contact.textContent = `${request.email} · ${request.phone}`;
                const details = document.createElement('p');
                details.textContent = `${request.propertyType} · ${request.preferredDateOrSchedule} · ${request.status}`;
                const address = document.createElement('p');
                address.textContent = request.propertyAddress;
                item.append(title, contact, details, address);
                if (dispatcher.role === 'Admin' || dispatcher.role === 'Sales') {
                    const status = document.createElement('select');
                    status.className = 'status-select';
                    ['Pending', 'Contacted', 'In Progress', 'Quoted', 'Closed', 'Declined'].forEach(value => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = value;
                        option.selected = request.status === value;
                        status.appendChild(option);
                    });
                    status.addEventListener('change', async () => {
                        try {
                            const response = await fetch('<?= site_url('quote-requests/status') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new URLSearchParams({ id: request.id, status: status.value })
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) throw new Error(result.error || 'Could not update quote request.');
                            request.status = status.value;
                            details.textContent = `${request.propertyType} · ${request.preferredDateOrSchedule} · ${request.status}`;
                            showAlert('dispatcherAlert', 'Quote request status updated.', 'success');
                        } catch (error) {
                            showAlert('dispatcherAlert', error.message, 'error');
                        }
                    });
                    item.appendChild(status);
                }
                container.appendChild(item);
            });
        }

        async function renderServices() {
            const container = document.getElementById('servicesList');
            const result = await requestCatalog('catalog/services');
            container.replaceChildren();
            result.services.forEach(service => {
                const item = document.createElement('article');
                item.className = 'data-item';
                item.innerHTML = `${service.photo ? `<img class="item-photo" src="${escapeHtml(service.photo)}" alt="">` : '<div class="photo-placeholder">SERVICE PHOTO</div>'}<h3>${escapeHtml(service.name)}</h3><p><strong>Type:</strong> ${escapeHtml(service.type)}</p><p>${escapeHtml(service.description)}</p><div class="data-actions"><button class="btn-small btn-edit" type="button">Edit</button><button class="btn-small btn-delete" type="button">Delete</button></div>`;
                item.querySelector('.btn-edit').onclick = () => editService(service);
                item.querySelector('.btn-delete').onclick = async () => {
                    if (!confirm('Delete this service?')) return;
                    try { await requestCatalog('catalog/services/delete', 'POST', { id: service.id }); await renderServices(); showAlert('dispatcherAlert', 'Service deleted.', 'success'); }
                    catch (error) { showAlert('dispatcherAlert', error.message, 'error'); }
                };
                container.appendChild(item);
            });
            if (!result.services.length) container.innerHTML = '<p class="empty-state">No services listed.</p>';
        }

        async function renderProducts() {
            const container = document.getElementById('productsList');
            const result = await requestCatalog('catalog/products');
            container.replaceChildren();
            result.products.forEach(product => {
                const item = document.createElement('article');
                item.className = 'data-item';
                item.innerHTML = `${product.photo ? `<img class="item-photo" src="${escapeHtml(product.photo)}" alt="">` : '<div class="photo-placeholder">PRODUCT PHOTO</div>'}<h3>${escapeHtml(product.name)}</h3><p><strong>Category:</strong> ${escapeHtml(product.category)}</p><p><strong>Price:</strong> ${escapeHtml(product.price)}</p><p>${escapeHtml(product.description)}</p><div class="data-actions"><button class="btn-small btn-edit" type="button">Edit</button><button class="btn-small btn-delete" type="button">Delete</button></div>`;
                item.querySelector('.btn-edit').onclick = () => editProduct(product);
                item.querySelector('.btn-delete').onclick = async () => {
                    if (!confirm('Delete this product?')) return;
                    try { await requestCatalog('catalog/products/delete', 'POST', { id: product.id }); await renderProducts(); showAlert('dispatcherAlert', 'Product deleted.', 'success'); }
                    catch (error) { showAlert('dispatcherAlert', error.message, 'error'); }
                };
                container.appendChild(item);
            });
            if (!result.products.length) container.innerHTML = '<p class="empty-state">No products listed.</p>';
        }

        function editService(service) {
            document.getElementById('serviceId').value = service.id;
            document.getElementById('serviceName').value = service.name;
            document.getElementById('serviceType').value = service.type;
            document.getElementById('serviceDescription').value = service.description;
            document.getElementById('serviceFormTitle').textContent = 'Edit Service';
        }

        function editProduct(product) {
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productFormTitle').textContent = 'Edit Product';
        }

        function resetServiceForm() { document.getElementById('serviceForm').reset(); document.getElementById('serviceId').value = ''; document.getElementById('serviceFormTitle').textContent = 'Add Service'; }
        function resetProductForm() { document.getElementById('productForm').reset(); document.getElementById('productId').value = ''; document.getElementById('productFormTitle').textContent = 'Add Product'; }

        <?php if ($canManageCatalog): ?>
        document.getElementById('serviceForm').addEventListener('submit', async event => {
            event.preventDefault();
            const id = document.getElementById('serviceId').value;
            const photo = await readPhoto(document.getElementById('servicePhoto').files[0]);
            const data = { id, name: document.getElementById('serviceName').value.trim(), type: document.getElementById('serviceType').value, description: document.getElementById('serviceDescription').value.trim(), photo };
            try {
                await requestCatalog('catalog/services/save', 'POST', data);
                resetServiceForm(); await renderServices(); showAlert('dispatcherAlert', 'Service saved.', 'success');
            } catch (error) { showAlert('dispatcherAlert', error.message, 'error'); }
        });

        document.getElementById('productForm').addEventListener('submit', async event => {
            event.preventDefault();
            const id = document.getElementById('productId').value;
            const photo = await readPhoto(document.getElementById('productPhoto').files[0]);
            const data = { id, name: document.getElementById('productName').value.trim(), category: document.getElementById('productCategory').value, price: document.getElementById('productPrice').value.trim(), description: document.getElementById('productDescription').value.trim(), photo };
            try {
                await requestCatalog('catalog/products/save', 'POST', data);
                resetProductForm(); await renderProducts(); showAlert('dispatcherAlert', 'Product saved.', 'success');
            } catch (error) { showAlert('dispatcherAlert', error.message, 'error'); }
        });
        <?php endif; ?>

        if (showBookings) {
            renderBookings().catch(error => showAlert('dispatcherAlert', error.message, 'error'));
            renderQuoteRequests().catch(error => showAlert('dispatcherAlert', error.message, 'error'));
        }
        <?php if ($canManageCatalog): ?>
        renderServices().catch(error => showAlert('dispatcherAlert', error.message, 'error'));
        renderProducts().catch(error => showAlert('dispatcherAlert', error.message, 'error'));
        <?php endif; ?>
    </script>
</body>
</html>
