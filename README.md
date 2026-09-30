# 🌞 Sun Son Solar - Web System

Complete web-based management system for Sun Son Solar, built with HTML, CSS, JavaScript, and SQLite.

---

## 📁 Project Files Structure

```
sun-son-solar/
├── index.html (or login.html)      # Login page - start here
├── register.html                    # User registration page
├── dashboard.html                   # Main dashboard after login
├── styles.css                       # Shared CSS styling
├── script.js                        # Shared JavaScript functions
├── sun_son_solar_setup.sql          # Database schema (SQLite)
└── README.md                        # This file
```

---

## 🚀 Getting Started

### Step 1: Set Up Database in VS Code

1. **Install SQLite Extension**
   - Open VS Code
   - Go to Extensions (Ctrl+Shift+X)
   - Search for "SQLite" 
   - Install "SQLite" by alexcvzz

2. **Create Database File**
   - Create a new file: `sun_son_solar.db` in your project folder
   - Right-click the `.db` file → "Open Database"
   - The database will appear in the SQLite Explorer panel

3. **Run Setup Script**
   - Open the database in SQLite Explorer
   - Copy all content from `sun_son_solar_setup.sql`
   - Right-click the database → "Run Query"
   - Paste and execute the SQL script

4. **Verify Tables**
   - Expand the database in SQLite Explorer
   - You should see these tables:
     - users
     - customers
     - employees
     - products
     - services
     - technician_checkin

---

## 🔐 Default Login Credentials

The system comes with 2 pre-configured admin accounts:

| Username | Password | Role |
|----------|----------|------|
| Kitty Kat16 | K@tSunShine16 | Admin |
| admin | admin 123 | Admin |

---

## 📄 File Descriptions

### **login.html**
- First page users see
- Inspired by clean, minimal design with yellow/blue colors
- Contains login form with username/password fields
- Has "Register" link for new users
- Simple and human-friendly interface

### **register.html**
- New user registration page
- Two account types: Customer or Employee
- For Customers: Collects basic personal info
- For Employees: Collects personal info + department selection
- Departments: Technician, Dispatcher, Admin
- Form validation before submission

### **dashboard.html**
- Main page after user logs in
- Shows different content based on user role:
  - **All Users**: Overview, Products, Services
  - **Technicians**: + Check-In tab (GPS location tracking)
  - **Admins**: + Admin Dashboard tab (view all users, check-in logs, statistics)
- User profile summary at top
- Tab-based navigation for different sections

### **styles.css**
- Shared styling for all pages
- Color scheme:
  - Primary Blue: #1a73e8
  - Accent Yellow: #ffd400
  - Background Gray: #f4f6f8
- Responsive design for mobile/tablet/desktop
- Button styles, form styling, alerts, cards, tables
- All components use consistent theme

### **script.js**
- Shared JavaScript functions
- **Authentication Functions**:
  - `login()` - User login
  - `registerUser()` - User registration
  - `logout()` - User logout
  - `loadUserFromSession()` - Check if user logged in

- **Data Functions**:
  - `getProducts()` - Fetch all products
  - `getServices()` - Fetch all services
  - `getAllUsers()` - Get all users
  - `getCustomers()` - Get all customers
  - `getEmployees()` - Get all employees

- **Check-In Functions**:
  - `getCurrentLocation()` - Get GPS coordinates
  - `submitCheckin()` - Save technician check-in
  - `getCheckins()` - Fetch all check-ins

- **Utility Functions**:
  - `showAlert()` - Display messages to users
  - `validateFormData()` - Validate form inputs
  - `isLoggedIn()` - Check login status
  - `isAdmin()`, `isTechnician()` - Check user role

### **sun_son_solar_setup.sql**
- SQLite database schema file
- Creates 6 tables:
  - **users**: All user accounts
  - **customers**: Customer personal information
  - **employees**: Employee information + department
  - **products**: Solar products catalog
  - **services**: Services offered
  - **technician_checkin**: GPS check-in records
- Includes sample data (products, services)
- Pre-configured admin users

---

## 🎯 Features

### 1. **User Authentication**
- Secure login system
- User registration with account type selection
- Customer accounts for clients
- Employee accounts for staff (with departments)
- Session management with localStorage

### 2. **Products & Services**
- View all solar products (panels, inverters, batteries, wiring, racking)
- View all services (consultation, design, installation, maintenance, etc.)
- Organized grid layout for easy browsing

### 3. **Technician Check-In**
- One-click GPS location capture
- Automatic timestamp recording
- Shows current coordinates and accuracy
- View recent check-in history

### 4. **Admin Dashboard**
- View all registered users
- Monitor check-in logs
- View system statistics (total users, products, services)
- Accessible only to Admin role

### 5. **Responsive Design**
- Works on desktop, tablet, and mobile devices
- Mobile-optimized layouts
- Touch-friendly buttons and forms
- Adaptive grid system

---

## 🔄 How to Use

### **For New Users**

1. Open `login.html` in a web browser
2. Click "Register" button
3. Choose account type (Customer/Employee)
4. Fill in your information:
   - First name, Last name
   - Birthdate, Gender
   - Email, Phone
   - Address
   - Username (unique)
   - Password (minimum 6 characters)
5. If Employee, select department (Technician/Dispatcher/Admin)
6. Click "Create Account"
7. Login with your new credentials

### **For Customers**

1. Login with your username/password
2. View dashboard overview
3. Browse Products section
4. Browse Services section
5. Logout when done

### **For Technicians**

1. Login with your username/password
2. Access all customer features
3. Click "Check-In" tab
4. Click "Get My Location" (allows browser to access GPS)
5. Location coordinates appear
6. Click "Check In Now" to save with timestamp
7. View recent check-ins below

### **For Admins**

1. Login with admin credentials
2. Access all customer features
3. Access "Check-In" tab (like technicians)
4. Click "Admin" tab to see:
   - All registered users in system
   - All technician check-in records
   - System statistics

---

## 🗄️ Database Tables

### **users**
- `id` - Unique user ID (Primary Key)
- `username` - Unique username
- `password` - User password
- `role` - User role (Admin, Technician, Dispatcher, Customer)
- `created_at` - Account creation timestamp
- `is_active` - Account status

### **customers**
- `id` - Customer ID (Primary Key)
- `user_id` - Reference to users table
- `first_name`, `last_name`, `middle_name`
- `birthdate`, `gender`
- `email`, `phone_number`, `address`
- `created_at` - Registration timestamp

### **employees**
- `id` - Employee ID (Primary Key)
- `user_id` - Reference to users table
- `first_name`, `last_name`, `middle_name`
- `birthdate`, `gender`
- `email`, `phone_number`, `address`
- `department` - Employee's department
- `created_at` - Hire date/registration timestamp

### **products**
- `id` - Product ID (Primary Key)
- `product_name` - Product name
- `category` - Product category
- `description` - Product details
- `price` - Product price
- `created_at` - Date added

### **services**
- `id` - Service ID (Primary Key)
- `service_name` - Service name
- `service_type` - Type of service
- `description` - Service details
- `created_at` - Date added

### **technician_checkin**
- `id` - Check-in ID (Primary Key)
- `employee_id` - Employee who checked in
- `employee_name` - Employee name
- `check_in_time` - GPS timestamp
- `latitude`, `longitude` - GPS coordinates
- `address` - Location address
- `created_at` - Record creation time

---

## 💾 Data Storage

### **Current System (Development)**
- Uses browser's **localStorage** for data persistence
- Data stored locally on user's device
- Data persists across browser sessions
- No server required

### **Production Implementation**
To connect with actual SQLite database:
1. Set up a backend server (Node.js, Python, etc.)
2. Create API endpoints for:
   - POST `/api/login` - User authentication
   - POST `/api/register` - New user registration
   - GET `/api/products` - Fetch products
   - GET `/api/services` - Fetch services
   - POST `/api/checkin` - Save technician check-in
   - GET `/api/users` (admin only) - Fetch all users
3. Replace localStorage calls with API fetch requests in `script.js`
4. Update database connection strings

---

## 🎨 Design Features

### **Color Scheme**
- **Primary**: #1a73e8 (Professional Blue)
- **Accent**: #ffd400 (Solar Yellow)
- **Background**: #f4f6f8 (Light Gray)
- **Text**: #333 (Dark)

### **Design Elements**
- Clean, minimal interface
- High contrast for readability
- Rounded corners on cards
- Subtle shadows for depth
- Color-coded alerts (green=success, red=error, yellow=warning)
- Hover effects for interactivity

### **User Experience**
- Simple, intuitive navigation
- Tab-based content organization
- Form validation with clear error messages
- Loading states and feedback
- Mobile-first responsive design

---

## 📱 Browser Compatibility

- Chrome/Chromium (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## ⚙️ Configuration & Customization

### **Change Logo/Branding**
In all HTML files, find:
```html
<div class="header-logo">🌞 Sun Son Solar</div>
```
Replace text as needed.

### **Change Colors**
Edit `styles.css`:
```css
:root {
  --primary-color: #1a73e8;  /* Change blue */
  --accent-color: #ffd400;   /* Change yellow */
}
```

### **Add New Products**
In `script.js`, find `initializeApp()` function and add to products array:
```javascript
{ 
  id: 6, 
  name: 'New Product', 
  category: 'Category', 
  price: '$X,XXX', 
  description: 'Description' 
}
```

### **Add New Services**
In `script.js`, find `initializeApp()` function and add to services array:
```javascript
{ 
  id: 8, 
  name: 'Service Name', 
  type: 'Service Type', 
  description: 'Description' 
}
```

---

## 🐛 Troubleshooting

### **Can't log in**
- Check username and password are correct
- Try clearing browser cache (Ctrl+Shift+Delete)
- Check if localStorage is enabled in browser

### **Location not working**
- Browser must have permission to access GPS
- Check browser settings for location permissions
- Some browsers may require HTTPS for GPS

### **Data not persisting**
- Ensure browser's localStorage is enabled
- Don't use private/incognito mode (data clears on close)
- Check browser storage limits

### **Database file not appearing**
- Ensure you created `.db` file in project folder
- Restart VS Code
- Re-install SQLite extension if needed

---

## 📝 Notes for Developer

### **Code Comments**
- All files include detailed comments explaining functionality
- Function names are self-descriptive
- Comments use clear, simple language

### **Testing Users**
Create test accounts:
1. Register new customer
2. Register new technician employee
3. Register new dispatcher employee
4. Test with admin account (Kitty Kat16)

### **Best Practices Used**
- Validation before data submission
- Error handling with user feedback
- Responsive design patterns
- Accessibility considerations
- Clear separation of concerns

---

## 📞 Support

For issues or questions:
1. Check error messages in browser console (F12 → Console)
2. Verify all files are in same folder
3. Ensure CSS and JS files are linked correctly
4. Check SQLite extension is installed and database is created

---

## 📄 License & Credits

**Creator**: Sun Son Solar Inc.
**Founder**: Katherine (Kat) Sinagaraw
**Co-founder**: Santino Sinagaraw (Sonny)
**System Developed**: 2026

---

## ✅ Checklist Before Going Live

- [ ] All files downloaded and in same folder
- [ ] SQLite database created with tables
- [ ] Tested login with both admin accounts
- [ ] Tested customer registration
- [ ] Tested employee registration
- [ ] Tested technician check-in (with GPS)
- [ ] Tested admin dashboard features
- [ ] Verified responsive design on mobile
- [ ] Tested logout functionality
- [ ] Checked all links work correctly

---

**Version**: 1.0
**Last Updated**: September 2026
**Status**: Ready for Development/Testing
