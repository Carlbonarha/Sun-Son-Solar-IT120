-- ============================================
-- Sun Son Solar Database (Converted for MySQL/XAMPP)
-- ============================================

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'IT', 'Dispatch', 'Dispatcher', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service', 'Technician', 'Maintenance', 'Customer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    approval_status VARCHAR(20) NOT NULL DEFAULT 'Approved'
);

-- 2. Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    middle_name VARCHAR(255),
    birthdate DATE,
    gender VARCHAR(50),
    email VARCHAR(255) UNIQUE,
    phone_number VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Free quote requests are separate from scheduled service bookings.
CREATE TABLE IF NOT EXISTS quote_requests (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    full_name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    service_needed VARCHAR(255) NOT NULL,
    property_address TEXT NOT NULL,
    property_type VARCHAR(255) NOT NULL,
    average_monthly_electricity_bill VARCHAR(100) NOT NULL,
    preferred_contact_method VARCHAR(100) NOT NULL,
    additional_details TEXT,
    preferred_date_or_schedule VARCHAR(255) NOT NULL,
    attachment_path VARCHAR(255),
    privacy_consent TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    assigned_department VARCHAR(100) NULL,
    assigned_employee_id INT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_quote_requests_customer_id (customer_id),
    INDEX idx_quote_requests_created_at (created_at),
    INDEX idx_quote_requests_assigned_employee (assigned_employee_id),
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 3. Employees Table
CREATE TABLE IF NOT EXISTS employees (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    middle_name VARCHAR(255),
    birthdate DATE,
    gender VARCHAR(50),
    email VARCHAR(255) UNIQUE,
    phone_number VARCHAR(50),
    address TEXT,
    department ENUM('Admin', 'IT', 'Dispatch', 'Dispatcher', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service', 'Technician', 'Maintenance') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

ALTER TABLE quote_requests
    ADD CONSTRAINT fk_quote_requests_assigned_employee
    FOREIGN KEY (assigned_employee_id) REFERENCES employees(id)
    ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS request_messages (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    request_type VARCHAR(20) NOT NULL,
    request_id INT NOT NULL,
    sender_user_id INT NULL,
    sender_name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    INDEX idx_request_messages_thread (request_type, request_id, created_at),
    INDEX idx_request_messages_sender (sender_user_id)
);

CREATE TABLE IF NOT EXISTS customer_reviews (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    customer_name VARCHAR(255) NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    story TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    privacy_consent TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    published_at DATETIME NULL,
    INDEX idx_customer_reviews_status (status),
    INDEX idx_customer_reviews_customer (customer_id),
    INDEX idx_customer_reviews_published (published_at),
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 4. Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    category ENUM('Panels', 'Inverters', 'Batteries', 'Racking and Mounting', 'Wires') NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    service_type ENUM('Consultation', 'Designing', 'Permitting', 'Installation', 'Maintenance', 'Repair', 'Monitoring') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Technician Check-in Table
CREATE TABLE IF NOT EXISTS technician_checkin (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- ============================================
-- INSERT INITIAL DATA
-- ============================================

-- Initial admin accounts are provisioned by the InitialUsers migration.

-- Magpasok ng Sample Products
INSERT INTO products (product_name, category, description, price) VALUES 
('High-Efficiency Solar Panels', 'Panels', 'High-efficiency solar panels with 25-year warranty', 350.00),
('Solar Inverters', 'Inverters', 'Convert DC to AC power efficiently', 2000.00),
('Energy Storage Batteries', 'Batteries', 'Lithium energy storage solutions for backup power', 5000.00),
('Racking Systems', 'Racking and Mounting', 'Durable mounting systems for any roof type', 1200.00),
('Solar Wiring Kit', 'Wires', 'Safe and certified solar electrical wiring', 300.00);

-- Magpasok ng Sample Services
INSERT INTO services (service_name, service_type, description) VALUES 
('Free Consultation', 'Consultation', 'Free initial consultation to assess your energy needs'),
('System Design', 'Designing', 'Custom solar system design by our engineers'),
('Permit Handling', 'Permitting', 'Handle all necessary permits and documentation'),
('Professional Installation', 'Installation', 'Professional installation by certified technicians'),
('Regular Maintenance', 'Maintenance', 'Regular system maintenance and cleaning'),
('Quick Repairs', 'Repair', 'Quick repair services for system issues'),
('24/7 Monitoring', 'Monitoring', '24/7 system performance monitoring');
