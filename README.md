# University of Liverpool - Student Course Hub

A comprehensive web application for managing university programmes, modules, staff, and student interests. Built with PHP and MySQL.

## Features

### Public Portal
- Browse undergraduate and postgraduate programmes
- View detailed programme information with modules
- View staff profiles and contact information
- Register interest in programmes (requires student account)
- Accessible and responsive design (WCAG 2.1 AA compliant)

### Student Portal
- Create student account and login
- Register interest in multiple programmes
- Manage programme interests from dashboard
- Update profile information and password
- View registered interests with programme details

### Staff Portal
- Secure staff login with password authentication
- View modules you lead and related programmes
- Track students interested in your programmes
- Filter interested students by programme
- Export student interest data to CSV
- Update profile information, photo, and password

### Admin Panel
- Complete programme management (create, edit, publish/draft)
- Module management with staff assignment
- Staff member management with photos
- View student interest registrations
- Dashboard with statistics and insights
- Contact form message management

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Vanilla JavaScript, Custom CSS
- **Server**: Apache (XAMPP recommended for local development)

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- XAMPP (recommended) or similar LAMP/WAMP stack

### Setup Instructions

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
   - Option A: Use phpMyAdmin
     - Open phpMyAdmin
     - Create a new database named `student_course_hub`
     - Import `example-data.sql`
   
   - Option B: Use command line
     ```bash
     mysql -u root -p < example-data.sql
     ```

4. **Run database migrations**
   - Visit: `http://localhost/studenthub/admin/setup_database.php`
   - This will add all required columns and tables
   - Follow the on-screen instructions

5. **Set up file permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/staff/
   ```

6. **Access the application**
   - Public site: `http://localhost/studenthub/public/`
   - Admin panel: `http://localhost/studenthub/admin/`
   - Staff portal: `http://localhost/studenthub/staff/`
   - Student portal: `http://localhost/studenthub/students/`

## Default Credentials

### Admin Account
- **Email**: admin@liverpool.ac.uk
- **Password**: admin123
- Create admin account via: `admin/signup.php`

### Staff Accounts
- Staff members need to be created by admin
- Default password format: `password123` (change after first login)
- Email format: `firstname.lastname@liverpool.ac.uk`

### Student Accounts
- Students can self-register at: `students/register.php`
- Minimum password length: 8 characters

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
├── example-data.sql    # Sample database with data
├── migration-update.sql # Database migration script
└── DATABASE_SETUP_GUIDE.md # Detailed setup guide
```

## Database Schema

### Main Tables
- **levels** - Programme levels (Undergraduate, Postgraduate)
- **staff** - Staff members with authentication
- **modules** - Course modules with leaders
- **programmes** - Degree programmes
- **programmemodules** - Module assignments to programmes
- **students** - Student accounts
- **interestedstudents** - Student interest registrations
- **admin** - Admin user accounts
- **contact_messages** - Contact form submissions

## Key Features Explained

### Programme Management
- Create programmes with levels (UG/PG)
- Assign programme leaders
- Add/remove modules to programmes by year
- Publish or save as draft
- Upload programme images

### Module Management
- Create modules with descriptions
- Assign module leaders from staff
- Link modules to multiple programmes
- Track which programmes use each module

### Interest Registration
- Students must create account to register interest
- Track registration dates
- Staff can view interested students for their programmes
- Export functionality for staff

### Authentication System
- Three separate authentication systems:
  - Admin (full access)
  - Staff (view own modules/programmes)
  - Students (manage own interests)
- Password hashing with PHP password_hash()
- Session-based authentication
- Secure logout functionality

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for all database queries
- Session management with regeneration
- Input validation and sanitization
- XSS protection with htmlspecialchars()
- File upload validation (type, size)
- CSRF protection ready (can be enhanced)

## Customization

### Branding
- Update university name in `includes/header.php`
- Modify colors in `css/university.css` (CSS custom properties)
- Replace logo in navigation

### Email Configuration
- Staff emails generated as: `firstname.lastname@liverpool.ac.uk`
- Update domain in `admin/setup_database.php` if needed

### File Upload Limits
- Programme images: 5MB max
- Staff photos: 5MB max
- Allowed formats: JPG, PNG, GIF, WebP
- Configure in respective management pages

## Troubleshooting

### Database Connection Issues
- Check credentials in `config/db.php`
- Ensure MySQL service is running
- Verify database exists

### File Upload Issues
- Check folder permissions: `chmod 755 uploads/`
- Verify PHP upload_max_filesize in php.ini
- Check post_max_size in php.ini

### Session Issues
- Ensure session.save_path is writable
- Check PHP session configuration
- Clear browser cookies

### Duplicate Data Issues
- Run `admin/setup_database.php` to fix schema
- Check for duplicate entries in database
- Review foreign key constraints

## Development

### Adding New Features
1. Follow existing code structure
2. Use prepared statements for database queries
3. Implement proper error handling
4. Add input validation
5. Test with different user roles

### Code Standards
- Use meaningful variable names
- Add comments for complex logic
- Follow existing naming conventions
- Maintain consistent indentation
- Use htmlspecialchars() for output

## Support & Documentation

- **Setup Guide**: See `DATABASE_SETUP_GUIDE.md`
- **Database Migrations**: Run `admin/setup_database.php`
- **Accessibility**: WCAG 2.1 AA compliant
- **Browser Support**: Modern browsers (Chrome, Firefox, Safari, Edge)

## License

This project is developed for University of Liverpool.

## Credits

Developed as a student course management system for higher education institutions.

---

**Version**: 1.0  
**Last Updated**: March 2026  
**Institution**: University of Liverpool
