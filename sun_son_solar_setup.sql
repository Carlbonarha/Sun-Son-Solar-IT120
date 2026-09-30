-- ============================================
-- Sun Son Solar Database (SQLite)
-- ============================================
-- This file creates all tables needed for the system
-- Run this in VS Code SQLite extension to set up the database

-- Users Table
-- Stores all user accounts (Admin, Technician, Dispatcher, Customer)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('Admin', 'Technician', 'Dispatcher', 'Customer')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Customers Table
-- Stores customer personal information
CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    middle_name TEXT,
    birthdate DATE,
    gender TEXT,
    email TEXT UNIQUE,
    phone_number TEXT,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Employees Table
-- Stores employee information including department
CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    middle_name TEXT,
    birthdate DATE,
    gender TEXT,
    email TEXT UNIQUE,
    phone_number TEXT,
    address TEXT,
    department TEXT NOT NULL CHECK(department IN ('Technician', 'Dispatcher', 'Admin')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Products Table
-- Stores solar product information
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_name TEXT NOT NULL,
    category TEXT NOT NULL CHECK(category IN ('Panels', 'Inverters', 'Batteries', 'Racking and Mounting', 'Wires')),
    description TEXT,
    price REAL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services Table
-- Stores service information
CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_name TEXT NOT NULL,
    service_type TEXT NOT NULL CHECK(service_type IN ('Consultation', 'Designing', 'Permitting', 'Installation', 'Maintenance', 'Repair', 'Monitoring')),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Technician Check-in Table
-- Stores technician location and time tracking
CREATE TABLE IF NOT EXISTS technician_checkin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    employee_name TEXT NOT NULL,
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    latitude REAL,
    longitude REAL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- ============================================
-- INSERT INITIAL DATA
-- ============================================

-- Insert Admin Users (from Kat and Sol)
INSERT INTO users (username, password, role) VALUES ('Kitty Kat16', 'K@tSunShine16', 'Admin');
INSERT INTO users (username, password, role) VALUES ('admin', 'admin 123', 'Admin');

-- Insert Sample Products
INSERT INTO products (product_name, category, description, price) VALUES 
('High-Efficiency Solar Panels', 'Panels', 'High-efficiency solar panels with 25-year warranty', 350.00),
('Solar Inverters', 'Inverters', 'Convert DC to AC power efficiently', 2000.00),
('Energy Storage Batteries', 'Batteries', 'Lithium energy storage solutions for backup power', 5000.00),
('Racking Systems', 'Racking and Mounting', 'Durable mounting systems for any roof type', 1200.00),
('Solar Wiring Kit', 'Wires', 'Safe and certified solar electrical wiring', 300.00);

-- Insert Sample Services
INSERT INTO services (service_name, service_type, description) VALUES 
('Free Consultation', 'Consultation', 'Free initial consultation to assess your energy needs'),
('System Design', 'Designing', 'Custom solar system design by our engineers'),
('Permit Handling', 'Permitting', 'Handle all necessary permits and documentation'),
('Professional Installation', 'Installation', 'Professional installation by certified technicians'),
('Regular Maintenance', 'Maintenance', 'Regular system maintenance and cleaning'),
('Quick Repairs', 'Repair', 'Quick repair services for system issues'),
('24/7 Monitoring', 'Monitoring', '24/7 system performance monitoring');
