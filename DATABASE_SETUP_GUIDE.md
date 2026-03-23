# Database Setup Guide

This guide will help you set up the database with the new schema while preserving all existing data from `example-data.sql`.

## ⚠️ IMPORTANT: Auto-Increment Fix

The original `example-data.sql` had a critical issue where `Staff` and `Modules` tables were missing `AUTO_INCREMENT` on their primary keys. This prevented adding new staff and modules.

**This has been fixed in the updated files!**

If you already imported the old `example-data.sql`, you MUST run the migration to fix this issue.

## Quick Setup (Recommended)

### Step 1: Import the Base Database
1. Open phpMyAdmin or your MySQL client
2. Import `example-data.sql` - this will create the database with all the sample data

### Step 2: Run the Migration
Choose ONE of these methods:

#### Option A: Web-Based Migration (Easiest)
1. Make sure your web server is running
2. Navigate to: `http://localhost/[your-project]/admin/setup_database.php`
3. The script will automatically add all missing columns and tables
4. You'll see a success message when complete

#### Option B: SQL Migration
1. Open phpMyAdmin or your MySQL client
2. Select the `student_course_hub` database
3. Import `migration-update.sql`
4. This will add all necessary columns and tables

## What Gets Added

The migration adds the following to your database:

### Critical Fixes
- **Auto-increment for Staff table** - Enables adding new staff members
- **Auto-increment for Modules table** - Enables adding new modules

### Staff Table Updates
- `Email` - Email addresses (auto-generated as name@liverpool.ac.uk)
- `Phone` - Phone numbers (optional)
- `Bio` - Staff biography/description (optional)
- `Photo` - Profile photo filename (optional)
- `password` - Hashed password for staff login (optional)

### Programmes Table Updates
- `IsPublished` - Flag to show/hide programmes (default: 1/published)

### New Tables Created
1. **students** - Student accounts for the portal
   - StudentID, FullName, Email, Phone, Password
   - CreatedAt, UpdatedAt timestamps

2. **admin** - Admin user accounts
   - AdminID, name, email, password, created_at
   - Default account: admin@liverpool.ac.uk / admin123

3. **contact_messages** - Contact form submissions
   - id, name, email, subject, message, created_at, is_read

### InterestedStudents Table Updates
- `StudentID` - Links to students table (optional, for registered students)
- Foreign key constraint to students table
- Index for better query performance

## Default Credentials

After setup, you can login with:

### Admin Account
- Email: `admin@liverpool.ac.uk`
- Password: `admin123`
- **⚠️ IMPORTANT: Change this password immediately after first login!**

### Staff Accounts
- Staff members don't have passwords by default
- They need to set their password through the admin panel or directly in the database
- Email format: `firstname.lastname@liverpool.ac.uk` (without "Dr.")

### Student Accounts
- No default student accounts
- Students can register at: `/students/register.php`

## Verification

After running the migration, verify everything is set up correctly:

1. Check that all tables exist:
   ```sql
   SHOW TABLES;
   ```
   You should see: admin, contact_messages, interestedstudents, levels, modules, programmemodules, programmes, staff, students

2. Check staff table structure:
   ```sql
   SHOW COLUMNS FROM staff;
   ```
   Should include: StaffID, Name, Email, Phone, Photo, password

3. Check interestedstudents table:
   ```sql
   SHOW COLUMNS FROM interestedstudents;
   ```
   Should include: InterestID, StudentID, ProgrammeID, StudentName, Email, RegisteredAt

## Troubleshooting

### "Table already exists" errors
- This is normal if you run the migration multiple times
- The script checks for existing columns/tables before creating them

### Foreign key constraint errors
- Make sure the students table is created before adding the foreign key
- The migration script handles this automatically

### Permission errors for uploads directory
- The script tries to create `uploads/` and `uploads/staff/` directories
- If it fails, manually create them and set permissions to 755 or 777

### Staff email addresses look wrong
- The migration auto-generates emails from staff names
- You can update them manually:
  ```sql
  UPDATE staff SET Email = 'correct.email@liverpool.ac.uk' WHERE StaffID = 1;
  ```

## Next Steps

After successful setup:

1. **Login to Admin Panel**
   - Go to `/admin/login.php`
   - Use: admin@liverpool.ac.uk / admin123
   - Change the password immediately

2. **Set Staff Passwords** (if needed)
   - Staff can't login until they have passwords
   - Admin can set passwords through the admin panel
   - Or update directly in database with hashed passwords

3. **Test Student Registration**
   - Go to `/students/register.php`
   - Create a test student account
   - Try registering interest in a programme

4. **Upload Staff Photos** (optional)
   - Staff can upload photos from `/staff/settings.php`
   - Photos are stored in `uploads/staff/`

5. **Add Programme Images** (optional)
   - Admin can add programme images through the admin panel
   - Images are stored in `uploads/`

## Data Preservation

The migration preserves ALL existing data:
- ✅ All 20 staff members
- ✅ All 31 modules
- ✅ All 10 programmes (5 undergraduate, 5 postgraduate)
- ✅ All programme-module relationships
- ✅ All 4 sample interested students

Only new columns and tables are added - no data is deleted or modified.
