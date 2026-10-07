-- ============================================
-- Sun Son Solar Database (Converted for MySQL/XAMPP)
-- ============================================

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Technician', 'Dispatcher', 'Customer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
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
    department ENUM('Technician', 'Dispatcher', 'Admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
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

-- Magpasok ng Admin Users (Galing sa data mo)
INSERT INTO users (username, password, role) VALUES ('Kitty Kat16', 'K@tSunShine16', 'Admin');
INSERT INTO users (username, password, role) VALUES ('admin', 'admin 123', 'Admin');

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
