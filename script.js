/* ============================================
   Sun Son Solar - Shared JavaScript
   ============================================
   Contains common functions for authentication,
   data management, and UI interactions
   
   Note: Currently uses localStorage for data storage.
   Will be replaced with backend database calls.
*/

// ============================================
// INITIALIZATION
// ============================================

// Initialize app on page load
document.addEventListener('DOMContentLoaded', function() {
  initializeApp();
  loadUserFromSession();
});

// Initialize app with sample data if not exists
function initializeApp() {
  if (!localStorage.getItem('initialized')) {
    // Initialize users
    const users = [
      { id: 1, username: 'Kitty Kat16', password: 'K@tSunShine16', role: 'Admin' },
      { id: 2, username: 'admin', password: 'admin 123', role: 'Admin' }
    ];
    localStorage.setItem('users', JSON.stringify(users));

    // Initialize products
    const products = [
      { id: 1, name: 'High-Efficiency Solar Panels', category: 'Panels', price: '$350', description: 'High-efficiency solar panels with 25-year warranty' },
      { id: 2, name: 'Solar Inverters', category: 'Inverters', price: '$2,000', description: 'Convert DC to AC power efficiently' },
      { id: 3, name: 'Energy Storage Batteries', category: 'Batteries', price: '$5,000', description: 'Lithium energy storage solutions for backup power' },
      { id: 4, name: 'Racking Systems', category: 'Racking and Mounting', price: '$1,200', description: 'Durable mounting systems for any roof type' },
      { id: 5, name: 'Solar Wiring Kit', category: 'Wires', price: '$300', description: 'Safe and certified solar electrical wiring' }
    ];
    localStorage.setItem('products', JSON.stringify(products));

    // Initialize services
    const services = [
      { id: 1, name: 'Free Consultation', type: 'Consultation', description: 'Free initial consultation to assess your energy needs' },
      { id: 2, name: 'System Design', type: 'Designing', description: 'Custom solar system design by our engineers' },
      { id: 3, name: 'Permit Handling', type: 'Permitting', description: 'Handle all necessary permits and documentation' },
      { id: 4, name: 'Professional Installation', type: 'Installation', description: 'Professional installation by certified technicians' },
      { id: 5, name: 'Regular Maintenance', type: 'Maintenance', description: 'Regular system maintenance and cleaning' },
      { id: 6, name: 'Quick Repairs', type: 'Repair', description: 'Quick repair services for system issues' },
      { id: 7, name: '24/7 Monitoring', type: 'Monitoring', description: '24/7 system performance monitoring' }
    ];
    localStorage.setItem('services', JSON.stringify(services));

    // Mark as initialized
    localStorage.setItem('initialized', 'true');
  }
}

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

// Function to perform user login
function login(username, password) {
  const users = JSON.parse(localStorage.getItem('users')) || [];
  const user = users.find(u => u.username === username && u.password === password);

  if (user) {
    // Store current session
    localStorage.setItem('currentUser', JSON.stringify(user));
    return { success: true, user: user };
  }
  return { success: false, error: 'Invalid username or password' };
}

// Function to register new user
function registerUser(formData) {
  const users = JSON.parse(localStorage.getItem('users')) || [];

  // Check if username already exists
  if (users.find(u => u.username === formData.username)) {
    return { success: false, error: 'Username already exists' };
  }

  // Check if email already exists
  const customers = JSON.parse(localStorage.getItem('customers')) || [];
  const employees = JSON.parse(localStorage.getItem('employees')) || [];
  const allAccounts = [...customers, ...employees];
  
  if (allAccounts.find(a => a.email === formData.email)) {
    return { success: false, error: 'Email already registered' };
  }

  // Create new user
  const newUser = {
    id: users.length + 1,
    username: formData.username,
    password: formData.password,
    role: formData.accountType === 'employee' ? formData.department : 'Customer'
  };

  users.push(newUser);
  localStorage.setItem('users', JSON.stringify(users));

  // Store customer or employee data
  if (formData.accountType === 'customer') {
    const customer = {
      userId: newUser.id,
      firstName: formData.firstName,
      lastName: formData.lastName,
      middleName: formData.middleName,
      birthdate: formData.birthdate,
      gender: formData.gender,
      email: formData.email,
      phone: formData.phone,
      address: formData.address
    };
    const customers = JSON.parse(localStorage.getItem('customers')) || [];
    customers.push(customer);
    localStorage.setItem('customers', JSON.stringify(customers));
  } else {
    const employee = {
      userId: newUser.id,
      firstName: formData.firstName,
      lastName: formData.lastName,
      middleName: formData.middleName,
      birthdate: formData.birthdate,
      gender: formData.gender,
      email: formData.email,
      phone: formData.phone,
      address: formData.address,
      department: formData.department
    };
    const employees = JSON.parse(localStorage.getItem('employees')) || [];
    employees.push(employee);
    localStorage.setItem('employees', JSON.stringify(employees));
  }

  return { success: true, user: newUser };
}

// Function to logout user
function logout() {
  localStorage.removeItem('currentUser');
  window.location.href = 'login.html';
}

// Function to load user from session
function loadUserFromSession() {
  const user = localStorage.getItem('currentUser');
  if (!user && window.location.pathname.includes('dashboard') || window.location.pathname.includes('products') || window.location.pathname.includes('services')) {
    window.location.href = 'login.html';
  }
  return user ? JSON.parse(user) : null;
}

// ============================================
// UI HELPER FUNCTIONS
// ============================================

// Function to show alert messages
function showAlert(elementId, message, type) {
  const alert = document.getElementById(elementId);
  if (alert) {
    alert.textContent = message;
    alert.className = `alert show alert-${type}`;
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
      alert.classList.remove('show');
    }, 5000);
  }
}

// Function to switch between tabs
function switchTab(tabName) {
  // Hide all tabs
  const tabs = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => tab.classList.remove('active'));

  // Remove active class from all buttons
  const buttons = document.querySelectorAll('.nav-tabs button');
  buttons.forEach(btn => btn.classList.remove('active'));

  // Show selected tab
  const selectedTab = document.getElementById(tabName);
  if (selectedTab) {
    selectedTab.classList.add('active');
  }

  // Mark button as active
  event.target.classList.add('active');
}

// ============================================
// DATA RETRIEVAL FUNCTIONS
// ============================================

// Function to get all products
function getProducts() {
  return JSON.parse(localStorage.getItem('products')) || [];
}

// Function to get all services
function getServices() {
  return JSON.parse(localStorage.getItem('services')) || [];
}

// Function to get all users
function getAllUsers() {
  return JSON.parse(localStorage.getItem('users')) || [];
}

// Function to get all customers
function getCustomers() {
  return JSON.parse(localStorage.getItem('customers')) || [];
}

// Function to get all employees
function getEmployees() {
  return JSON.parse(localStorage.getItem('employees')) || [];
}

// ============================================
// TECHNICIAN CHECK-IN FUNCTIONS
// ============================================

// Function to get user's current location
function getCurrentLocation() {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject('Geolocation not supported');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      position => {
        resolve({
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        });
      },
      error => {
        reject(error.message);
      }
    );
  });
}

// Function to submit technician check-in
function submitCheckin(latitude, longitude, address) {
  const currentUser = JSON.parse(localStorage.getItem('currentUser'));
  if (!currentUser) {
    return { success: false, error: 'User not logged in' };
  }

  const checkin = {
    id: Date.now(),
    userId: currentUser.id,
    userName: currentUser.username,
    timestamp: new Date().toLocaleString(),
    latitude: latitude,
    longitude: longitude,
    address: address
  };

  const checkins = JSON.parse(localStorage.getItem('checkins')) || [];
  checkins.push(checkin);
  localStorage.setItem('checkins', JSON.stringify(checkins));

  return { success: true, checkin: checkin };
}

// Function to get all check-ins
function getCheckins() {
  return JSON.parse(localStorage.getItem('checkins')) || [];
}

// ============================================
// VALIDATION FUNCTIONS
// ============================================

// Function to validate email format
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Function to validate password strength
function isValidPassword(password) {
  return password.length >= 6;
}

// Function to validate form data
function validateFormData(data, type) {
  const errors = [];

  if (!data.firstName || !data.lastName) {
    errors.push('First and last name are required');
  }

  if (!data.email || !isValidEmail(data.email)) {
    errors.push('Valid email is required');
  }

  if (!data.username || data.username.length < 3) {
    errors.push('Username must be at least 3 characters');
  }

  if (!data.password || !isValidPassword(data.password)) {
    errors.push('Password must be at least 6 characters');
  }

  if (type === 'employee' && !data.department) {
    errors.push('Department is required for employees');
  }

  return {
    isValid: errors.length === 0,
    errors: errors
  };
}

// ============================================
// DATE AND TIME FUNCTIONS
// ============================================

// Function to format date for display
function formatDate(date) {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(date).toLocaleDateString('en-US', options);
}

// Function to get current date in YYYY-MM-DD format
function getCurrentDate() {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Function to generate unique ID
function generateId() {
  return '_' + Math.random().toString(36).substr(2, 9);
}

// Function to clear form inputs
function clearForm(formId) {
  const form = document.getElementById(formId);
  if (form) {
    form.reset();
  }
}

// Function to redirect to page
function redirectTo(page) {
  window.location.href = page;
}

// Function to check if user is logged in
function isLoggedIn() {
  return localStorage.getItem('currentUser') !== null;
}

// Function to get current user
function getCurrentUser() {
  return JSON.parse(localStorage.getItem('currentUser'));
}

// Function to check if user has admin role
function isAdmin() {
  const user = getCurrentUser();
  return user && user.role === 'Admin';
}

// Function to check if user is technician
function isTechnician() {
  const user = getCurrentUser();
  return user && user.role === 'Technician';
}
