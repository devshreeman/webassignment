# Student Course Hub

A web application for marketing university degree programmes and collecting prospective student interest data.

## Project Information

**Course**: CTEC2712 - Web Application Development  
**Project Type**: Group Assignment  
**Institution**: De Montfort University 
**Academic Year**: 2025/2026

## Project Scenario

This application was developed for a UK university requiring a system to market undergraduate and postgraduate degree programmes to prospective students. The primary purpose is to collect contact details of prospective students interested in specific degree programmes, enabling the university to send targeted communications about:

- Open days and campus events
- Application deadlines
- Programme updates and changes
- Course-specific information

The system generates mailing lists that indicate exactly which programmes each student has expressed interest in, allowing for precise and relevant communication.

## Core Features

### Public-Facing Portal
- **Programme Browsing**: View undergraduate and postgraduate programmes with filtering by level
- **Programme Details**: Detailed information including modules, staff, entry requirements, and career prospects
- **Staff Profiles**: View academic staff members with photos, biographies, and contact information
- **Interest Registration**: Prospective students can register interest in multiple programmes
- **Contact Form**: General enquiry form for prospective students
- **Responsive Design**: Fully responsive layout optimized for mobile, tablet, and desktop
- **Accessibility**: WCAG 2.1 AA compliant for inclusive access

### Student Portal
- **Self-Registration**: Prospective students can create accounts independently
- **Secure Authentication**: Login system with password hashing and session management
- **Interest Management**: Register interest in multiple programmes and manage selections
- **Dashboard**: View all registered programme interests in one place
- **Profile Management**: Update personal information and change password

### Staff Portal
- **Module Leader Access**: Staff can view modules they lead
- **Student Interest Tracking**: View prospective students interested in their programmes
- **Programme Filtering**: Filter interested students by specific programmes
- **CSV Export**: Export mailing lists of interested students for targeted communications
- **Profile Management**: Update personal information, upload photo, and change password

### Admin Panel
- **Programme Management**: Create, edit, publish/unpublish programmes with images
- **Module Management**: Create and assign modules to programmes by year
- **Staff Management**: Add staff members with photos, biographies, and module assignments
- **Interest Analytics**: View dashboard with statistics on student interests
- **Mailing List Generation**: View and export lists of interested students by programme
- **Contact Message Management**: Review and respond to enquiries from contact form
- **Database Tools**: Setup and migration utilities for database management

## Technology Stack

- **Backend**: PHP 7.4+ (no frameworks - vanilla PHP)
- **Database**: MySQL 5.7+ with PDO for database interactions
- **Frontend**: Vanilla JavaScript (no jQuery or frameworks), Custom CSS (no Bootstrap or Tailwind)
- **Server**: Apache web server with mod_rewrite
- **Development Environment**: XAMPP/MAMP/LAMP stack recommended

### Design Approach
- **No Frameworks**: Built entirely with vanilla PHP, JavaScript, and CSS as per assignment requirements
- **Custom CSS**: All styling written from scratch using CSS custom properties (variables)
- **Semantic HTML**: Proper HTML5 semantic elements throughout
- **Progressive Enhancement**: Core functionality works without JavaScript
- **Mobile-First**: Responsive design built with mobile devices as priority

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (with mod_rewrite enabled)
- XAMPP (recommended) or similar LAMP/WAMP/MAMP stack

### Quick Setup

1. **Clone or download the project**
   ```bash
   git clone <repository-url>
   cd studenthub
   ```

2. **Configure database connection**
   - Edit `config/db.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'student_course_hub';
   $username = 'root';
   $password = '';
   ```

3. **Import database**
   - **Option A**: Use phpMyAdmin
     - Open phpMyAdmin
     - Create a new database named `student_course_hub`
     - Import `example-data.sql`
   
   - **Option B**: Use command line
     ```bash
     mysql -u root -p < example-data.sql
     ```

4. **Run database setup (Important!)**
   - Visit: `http://localhost/studenthub/admin/setup_database.php`
   - This will automatically add all required columns and tables
   - Fixes auto-increment issues for Staff and Modules tables
   - Creates admin, students, and contact_messages tables
   - Follow the on-screen instructions

5. **Set up file permissions** (Linux/Mac only)
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/staff/
   ```

6. **Access the application**
   - Public site: `http://localhost/studenthub/public/`
   - Admin panel: `http://localhost/studenthub/admin/`
   - Staff portal: `http://localhost/studenthub/staff/`
   - Student portal: `http://localhost/studenthub/students/`

## Default Credentials (For Testing)

### Admin Account
After running the database setup:
- **Email**: admin@liverpool.ac.uk
- **Password**: admin123
- **Access**: Full system administration
- **Note**: For demonstration purposes only - change in production

### Staff Accounts
- Staff members are created by admin through the admin panel
- Staff need passwords set through admin panel or database
- Email format: `firstname.lastname@liverpool.ac.uk`
- Sample staff data included in `example-data.sql` (20 staff members)
- **Email**: sophia.miller@liverpool.ac.uk
- **Password**: pass12345

### Student Accounts
- Students self-register at: `students/register.php`
- Minimum password length: 8 characters
- Email validation and duplicate checking included
- Sample interested students included in database
- **Email**: student@liverpool.ac.uk
- **Password**: student123

## Project Structure

```
studenthub/
├── admin/              # Admin panel pages
│   ├── dashboard.php
│   ├── manage_programmes.php
│   ├── manage_modules.php
│   ├── manage_staff.php
│   ├── edit_programmes.php
│   ├── edit_modules.php
│   ├── edit_staff.php
│   ├── manage_interests.php
│   ├── contact_messages.php
│   ├── admin_settings.php
│   ├── setup_database.php
│   ├── login.php
│   └── signup.php
├── staff/              # Staff portal pages
│   ├── dashboard.php
│   ├── interested_students.php
│   ├── settings.php
│   ├── login.php
│   └── logout.php
├── students/           # Student portal pages
│   ├── dashboard.php
│   ├── register.php
│   ├── login.php
│   ├── settings.php
│   └── logout.php
├── public/             # Public-facing pages
│   ├── index.php
│   ├── programme.php
│   ├── staff.php
│   ├── contact.php
│   ├── register_interest.php
│   ├── accessibility.php
│   └── privacy.php
├── includes/           # Shared components
│   ├── header.php
│   ├── footer.php
│   ├── admin_header.php
│   └── admin_footer.php
├── config/             # Configuration files
│   └── db.php
├── css/                # Stylesheets
│   └── university.css
├── uploads/            # User-uploaded files
│   └── staff/
├── assets/             # Logo and favicon files
├── example-data.sql    # Sample database with data
└── README.md           # This file
```

## Database Schema

### Main Tables
- **levels** - Programme levels (Undergraduate, Postgraduate)
- **staff** - Staff members with authentication, photos, and biographies
- **modules** - Course modules with descriptions and module leaders
- **programmes** - Degree programmes with images and publish status
- **programmemodules** - Module assignments to programmes by year
- **students** - Student accounts with authentication
- **interestedstudents** - Student interest registrations (links students to programmes)
- **admin** - Admin user accounts with authentication
- **contact_messages** - Contact form submissions with read status

### Sample Data Included (for demonstration)
- **20 staff members** across various departments (Computer Science, Engineering, Business, etc.)
- **31 modules** covering different subject areas
- **10 programmes** (5 undergraduate, 5 postgraduate)
- **Programme-module relationships** showing which modules belong to which programmes
- **4 sample interested students** demonstrating the interest registration system
- **1 admin account** for system administration

## Key Assignment Requirements Met

### Mailing List Generation
The application fulfills the core requirement of generating mailing lists that indicate exactly which programmes each prospective student has expressed interest in:

- **Interest Tracking**: Each student interest is linked to specific programmes
- **CSV Export**: Staff and admin can export interested student data to CSV format
- **Programme Filtering**: Filter mailing lists by specific programmes
- **Contact Details**: Captures student name, email, and programme interests
- **Targeted Communications**: Enables sending programme-specific information to interested students

### Data Management
- **Programme Information**: Comprehensive programme details including modules, staff, and requirements
- **Module Structure**: Modules organized by year and linked to programmes
- **Staff Profiles**: Academic staff with module leadership assignments
- **Student Interests**: Many-to-many relationship between students and programmes

### User Roles & Authentication
Three distinct user types with appropriate access levels:
- **Admin**: Full system access for managing all content and users
- **Staff**: View modules they lead and interested students for their programmes
- **Students**: Register interest in programmes and manage their selections

### Security Implementation
- Password hashing using PHP's `password_hash()` with bcrypt
- Prepared statements for all database queries (SQL injection prevention)
- Session-based authentication with session regeneration
- Input validation and output escaping (XSS prevention)
- Role-based access control

## Security Features

- **Password Security**: All passwords hashed using PHP's `password_hash()` with bcrypt
- **SQL Injection Protection**: Prepared statements for all database queries
- **Session Security**: Session management with regeneration on login
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: Output escaping with `htmlspecialchars()` on all user-generated content
- **File Upload Security**: Type validation, size limits, and secure filename generation
- **Authentication**: Separate authentication systems for admin, staff, and students
- **Access Control**: Role-based access control for different user types

## Assignment Deliverables

### Functional Requirements
- **Programme Marketing**: Public-facing pages to showcase degree programmes  
- **Interest Collection**: System to capture prospective student contact details  
- **Mailing List Generation**: Export functionality for programme-specific mailing lists  
- **Multi-Programme Interest**: Students can express interest in multiple programmes  
- **Targeted Communications**: Filter and export by specific programmes  
- **Content Management**: Admin panel for managing programmes, modules, and staff  
- **User Authentication**: Secure login systems for admin, staff, and students  
- **Data Relationships**: Proper database design with foreign keys and relationships

### Technical Requirements
- **No Frameworks**: Built with vanilla PHP, JavaScript, and CSS  
- **Database Design**: Normalized MySQL database with proper relationships  
- **Security**: Password hashing, prepared statements, input validation  
- **Responsive Design**: Mobile-first responsive layout  
- **Accessibility**: WCAG 2.1 AA compliance  
- **Code Quality**: Clean, commented, maintainable code  
- **Error Handling**: Proper error handling and user feedback

## Troubleshooting

### Database Connection Issues
- Check credentials in `config/db.php`
- Ensure MySQL service is running (start XAMPP/MAMP)
- Verify database `student_course_hub` exists
- Check PDO extension is enabled in PHP

### "Cannot add staff/modules" Error
- Run `admin/setup_database.php` to fix auto-increment
- This is a known issue with the original schema
- The setup script will fix it automatically

### File Upload Issues
- Check folder permissions: `chmod 755 uploads/` (Linux/Mac)
- Verify PHP `upload_max_filesize` in php.ini (default: 5MB)
- Check `post_max_size` in php.ini
- Ensure `uploads/` and `uploads/staff/` directories exist

### Session Issues
- Ensure `session.save_path` is writable
- Check PHP session configuration
- Clear browser cookies and cache
- Verify session is started in PHP files

### Page Not Found (404) Errors
- Check that you're accessing the correct URL path
- Ensure Apache mod_rewrite is enabled
- Verify file permissions are correct

## Development

### Code Standards
- **Comments**: Single-line `//` comments in PHP, `/* */` in CSS
- **Security**: Always use prepared statements and `htmlspecialchars()`
- **Naming**: Descriptive variable names, PascalCase for database columns
- **Error Handling**: Try-catch blocks for database operations
- **Validation**: Server-side validation for all user inputs
- **Sessions**: Check authentication on protected pages

### Adding New Features
1. Follow existing code structure and patterns
2. Use prepared statements for all database queries
3. Implement proper error handling with try-catch
4. Add input validation and sanitization
5. Test with different user roles (admin, staff, student)
6. Ensure responsive design for mobile devices
7. Maintain accessibility standards (WCAG 2.1 AA)

## Technical Details

### Responsive Design
- Fully responsive layout for mobile, tablet, and desktop
- Breakpoints: 320px, 480px, 640px, 1024px, 1440px
- Touch-optimized with 44px minimum touch targets
- Mobile-friendly navigation with hamburger menu
- Horizontal scrolling data tables on mobile

### Accessibility
- WCAG 2.1 AA compliant
- Semantic HTML structure
- ARIA labels and attributes
- Keyboard navigation support
- Proper heading hierarchy
- Color contrast ratios meet standards
- Screen reader friendly

### Browser Support
- Chrome/Edge (Chromium) 90+
- Firefox 88+
- Safari 14+
- Mobile Safari (iOS 14+)
- Chrome Mobile (Android 10+)

## Project Team

This is a group assignment project for CTEC2712: Web Application Development.

### Members
- Shreeman Bhandari
- Bibek Timsina
- Suraj Rai
- Rojal Karki

## Academic Context

**Course**: CTEC2712 - Web Application Development  
**Institution**: De Montfort University
**Academic Year**: 2025/2026  
**Project Type**: Group Assignment  
**Scenario**: Student Course Hub for UK University

## License

This project is an academic assignment and is not affiliated with or endorsed by the University of Liverpool as an institution. It is developed solely for educational purposes.

---

**Version**: 1.0  
**Last Updated**: March 2026  
**Built with**: Vanilla PHP, MySQL, Vanilla JavaScript, Custom CSS (No Frameworks)
