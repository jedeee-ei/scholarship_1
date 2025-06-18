# 📁 Laravel Scholarship Management System - Project Structure

## 🏗️ **Root Directory**

### **Configuration Files**
- `composer.json` - PHP dependencies and autoloading configuration
- `package.json` - Node.js dependencies for frontend build tools
- `vite.config.js` - Vite configuration for asset compilation
- `artisan` - Laravel's command-line interface
- `README.md` - Project documentation

---

## 📂 **Core Application Structure**

### **`/app` - Application Logic**

#### **`/app/Console`**
- Contains Artisan commands for CLI operations
- Custom commands for maintenance and data processing

#### **`/app/Helpers`**
- **`ScholarshipDataHelper.php`** - Centralized data provider for:
  - Departments and courses mapping
  - Scholarship types and subtypes
  - Subject curricula for all courses
  - Government benefactor types
  - BEU strands and grade levels
  - Course durations and year levels

#### **`/app/Http`**
- **`/app/Http/Controllers`** - Request handling logic
  - **`/Admin`** - Admin panel controllers
    - `DashboardController.php` - Admin dashboard with analytics
    - `ApplicationController.php` - Scholarship application management
    - `GranteeController.php` - Student/grantee management
    - `ArchiveController.php` - Archived student records
    - `AnnouncementController.php` - System announcements
    - `ImportExportController.php` - Excel import/export functionality
    - `DocumentController.php` - Document viewing and downloads
    - `UserManagementController.php` - User account management
    - `SettingsController.php` - System settings and configuration
    - `ScholarshipManagementController.php` - Scholarship program management
    - `ReportController.php` - Report generation
  - **`/Student`** - Student portal controllers
    - `DashboardController.php` - Student dashboard
    - `ScholarshipController.php` - Application submission
    - `ScholarshipTrackerController.php` - Application status tracking
  - **`/Auth`** - Authentication controllers
    - `LoginController.php` - User authentication
  - `DataController.php` - API endpoints for dynamic data

#### **`/app/Mail`**
- Email notification classes for scholarship updates

#### **`/app/Models`**
- **`User.php`** - User accounts (students and admins)
- **`ScholarshipApplication.php`** - Scholarship applications
- **`Grantee.php`** - Approved scholarship recipients
- **`ArchivedStudent.php`** - Historical student records
- **`Announcement.php`** - System announcements
- **`Scholarship.php`** - Scholarship program definitions
- **`SystemSetting.php`** - Application configuration

#### **`/app/Providers`**
- Service providers for dependency injection and bootstrapping

#### **`/app/Services`**
- Business logic services for complex operations

---

## 🗄️ **Database Structure**

### **`/database`**

#### **`/database/migrations`**
- Database schema definitions and modifications
- **Key Tables:**
  - `users` - User accounts and authentication
  - `scholarship_applications` - Application submissions
  - `grantees` - Approved scholarship recipients
  - `archived_students` - Historical records
  - `announcements` - System notifications
  - `scholarships` - Scholarship program definitions
  - `system_settings` - Application configuration

#### **`/database/seeders`**
- **`AdminSeeder.php`** - Default admin account
- **`SystemSettingSeeder.php`** - Default system configuration
- **`DatabaseSeeder.php`** - Seeder orchestration

---

## 🎨 **Frontend Assets**

### **`/public`**
- **`/public/css`** - Compiled stylesheets
  - **`/layouts`** - Layout-specific styles
    - `admin.css` - Admin panel styling
    - `welcome.css` - Landing page styling
    - `login.css` - Authentication forms
  - **`/pages`** - Page-specific styles
    - `dashboard.css` - Dashboard layouts
    - `applications.css` - Application management
    - `students.css` - Student management
    - `archives.css` - Archive management
    - `announcements.css` - Announcement system
- **`/public/js`** - JavaScript files
  - `script 1.js` - Main application scripts
- **`/public/images`** - Static images and assets
- `index.php` - Application entry point
- `favicon.ico` - Site icon

### **`/resources`**
- **`/resources/views`** - Blade templates
  - **`/layouts`** - Base layouts
    - `admin.blade.php` - Admin panel layout
    - `welcome.blade.php` - Landing page
    - `login.blade.php` - Authentication layout
    - `splashscreen.blade.php` - Loading screen
  - **`/admin`** - Admin panel views
    - `dashboard.blade.php` - Admin dashboard
    - `applications.blade.php` - Application management
    - `application-detail.blade.php` - Application details
    - `students.blade.php` - Student management
    - `archived-students.blade.php` - Archive management
    - `announcements.blade.php` - Announcement system
    - `reports.blade.php` - Report generation
    - `settings.blade.php` - System configuration
  - **`/student`** - Student portal views
    - `dashboard.blade.php` - Student dashboard
    - `tracker.blade.php` - Application tracking
  - **`/components`** - Reusable components
    - `breadcrumb.blade.php` - Navigation breadcrumbs
- **`/resources/css`** - Source stylesheets
  - `app.css` - Tailwind CSS configuration
- **`/resources/js`** - Source JavaScript
  - `app.js` - Main application entry
  - `bootstrap.js` - Framework initialization

---

## ⚙️ **Configuration**

### **`/config`**
- `app.php` - Application settings
- `auth.php` - Authentication configuration
- `database.php` - Database connections
- `mail.php` - Email configuration
- `filesystems.php` - File storage settings
- `cache.php` - Caching configuration
- `queue.php` - Queue system settings
- `logging.php` - Log management
- `services.php` - Third-party services
- `session.php` - Session management

---

## 🛣️ **Routing**

### **`/routes`**
- **`web.php`** - Web application routes
  - Authentication routes
  - Student portal routes
  - Admin panel routes
  - API endpoints for data
- **`console.php`** - Artisan command routes
- **`admin.php`** - Additional admin routes (if needed)

---

## 📦 **Dependencies & Build**

### **`/vendor`**
- Composer-managed PHP dependencies
- Laravel framework and packages

### **`/bootstrap`**
- Application bootstrapping files
- Framework initialization

### **`/storage`**
- **`/storage/app`** - File uploads and storage
- **`/storage/framework`** - Framework cache and sessions
- **`/storage/logs`** - Application logs

---

## 🔧 **Key Features by Directory**

### **Admin Panel (`/resources/views/admin`)**
- **Dashboard** - Analytics and overview
- **Applications** - Review and approve applications
- **Students/Grantees** - Manage student records
- **Archives** - Historical data management
- **Announcements** - System notifications
- **Reports** - Data export and reporting
- **Settings** - System configuration

### **Student Portal (`/resources/views/student`)**
- **Dashboard** - Personal overview
- **Applications** - Submit scholarship applications
- **Tracker** - Monitor application status

### **Data Management (`/app/Helpers`)**
- **ScholarshipDataHelper** - Centralized data source
- **Static Data** - Departments, courses, subjects
- **Dynamic Forms** - Scholarship type-specific fields

---

## 🚀 **Optimizations Implemented**

### **Performance**
- Removed API complexity for static data
- Consolidated CSS and JavaScript files
- Eliminated duplicate code and unused files
- Streamlined database queries

### **Maintainability**
- Centralized data in helper classes
- Consistent naming conventions
- Modular component structure
- Clean separation of concerns

### **Responsiveness**
- Mobile-first CSS design
- Responsive table solutions
- Touch-friendly interfaces
- Adaptive layouts for all screen sizes

---

This structure provides a clean, maintainable, and scalable scholarship management system with clear separation between admin and student functionality, optimized performance, and comprehensive data management capabilities.
