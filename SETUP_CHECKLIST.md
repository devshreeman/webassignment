# Student Course Hub - Setup & Verification Checklist

## Initial Setup

### 1. Database Setup
- [ ] Import `schema.sql` into MySQL/MariaDB
- [ ] Run `admin/setup_database.php` to:
  - Add password column to staff table
  - Populate levels table (Undergraduate/Postgraduate)
  - Create contact_messages table
  - Verify database integrity
  - Check uploads directory permissions

### 2. Configuration
- [ ] Update `config/db.php` with your database credentials
- [ ] Ensure `uploads/` directory exists and is writable (755 or 777)

### 3. Create Admin Account
- [ ] Access `admin/signup.php` to create your first admin account
- [ ] Login at `admin/login.php`

## Database Schema Verification

### Tables (7 required)
- [x] `admin` - Administrator accounts
- [x] `staff` - Staff members (with password column)
- [x] `programmes` - Degree programmes
- [x] `modules` - Course modules
- [x] `programmemodules` - Junction table linking programmes to modules
- [x] `levels` - Programme levels (Undergraduate/Postgraduate)
- [x] `interestedstudents` - Student interest registrations
- [x] `contact_messages` - Contact form submissions

### Key Columns Verified

#### staff table
- StaffID (PK)
- Name
- Email
- Bio
- Photo
- password (added by migration)

#### programmes table
- ProgrammeID (PK)
- ProgrammeName
- LevelID (FK → levels)
- ProgrammeLeaderID (FK → staff)
- Duration
- Description
- Image
- IsPublished

#### modules table
- ModuleID (PK)
- ModuleName
- ModuleLeaderID (FK → staff)
- Description
- Image
- IsPublished
- ~~StaffID~~ (removed - not in schema)
- ~~ProgrammeID~~ (removed - not in schema)

#### programmemodules table
- ProgrammeModuleID (PK)
- ProgrammeID (FK → programmes)
- ModuleID (FK → modules)
- Year

## Fixed Issues

### 1. Database Schema Issues
- ✅ Removed references to non-existent `StaffID` column in modules table
- ✅ Removed references to non-existent `ProgrammeID` column in modules table
- ✅ Added missing `password` column to staff table
- ✅ Created `contact_messages` table for contact form

### 2. Foreign Key Constraint Issues
- ✅ Programme deletion now properly deletes programmemodules first
- ✅ Module deletion now properly deletes programmemodules first
- ✅ Staff deletion checks for references before deleting
- ✅ Added proper error handling for all delete operations

### 3. SQL Query Fixes
- ✅ Fixed staff dashboard module query (proper JOIN through programmemodules)
- ✅ Fixed edit_staff.php module query (proper JOIN through programmemodules)
- ✅ Fixed manage_modules.php query (proper JOIN through programmemodules)
- ✅ Fixed edit_programmes.php module insertion (removed non-existent columns)

### 4. Frontend Issues
- ✅ Removed year pill from module cards on programme detail page
- ✅ Fixed programme image display (correct path prefix)
- ✅ Added programme image to detail page hero section
- ✅ Removed blob decoration from CTA section
- ✅ Fixed copyright section (removed invalid HTML and dot separator)
- ✅ Updated CTA button to scroll to programmes section

### 5. Admin Panel Issues
- ✅ Created contact messages admin page
- ✅ Added contact messages link to admin sidebar
- ✅ Contact form now saves to database
- ✅ Fixed blank screen issues on delete operations
- ✅ Added image upload for programmes (add & edit)

## Testing Checklist

### Public Pages
- [ ] Homepage loads and displays programmes
- [ ] Programme filtering by level works
- [ ] Programme search works
- [ ] Programme detail page displays correctly
- [ ] Module list displays by year
- [ ] Staff page displays all staff members
- [ ] Register interest form works
- [ ] Contact form saves to database
- [ ] Programme images display correctly
- [ ] Mobile responsive design works

### Admin Panel
- [ ] Admin login works
- [ ] Dashboard displays statistics
- [ ] Add programme works (with image upload)
- [ ] Edit programme works (with image upload)
- [ ] Delete programme works (no blank screen)
- [ ] Publish/unpublish programme works
- [ ] Add module works
- [ ] Edit module works
- [ ] Delete module works (no blank screen)
- [ ] Link module to programme works
- [ ] Add staff works
- [ ] Edit staff works (with photo upload)
- [ ] Delete staff works (checks references)
- [ ] View interest registrations works
- [ ] View contact messages works
- [ ] Mark contact message as read works

### Staff Portal
- [ ] Staff login works
- [ ] Dashboard displays modules led
- [ ] Dashboard displays related programmes
- [ ] Staff can view their teaching responsibilities

### Database Integrity
- [ ] No orphaned records (modules without valid leaders)
- [ ] No orphaned records (programmes without valid leaders)
- [ ] Foreign key constraints working properly
- [ ] Cascade deletes working for interestedstudents
- [ ] Programme-module relationships maintained correctly

## Common Issues & Solutions

### Blank Screen on Delete
**Cause:** Foreign key constraint violations
**Solution:** Delete operations now handle related records first

### Images Not Displaying
**Cause:** Incorrect relative paths
**Solution:** All image paths now use correct `../` prefix from public directory

### Staff Password Not Working
**Cause:** Missing password column in staff table
**Solution:** Run `admin/setup_database.php` to add the column

### Levels Not Available
**Cause:** Empty levels table
**Solution:** Run `admin/setup_database.php` to populate with default levels

### Contact Messages Not Saving
**Cause:** Missing contact_messages table
**Solution:** Run `admin/setup_database.php` to create the table

## File Structure

```
WebAssignment2/
├── admin/
│   ├── contact_messages.php (NEW)
│   ├── setup_database.php (NEW)
│   ├── add_staff_password_column.php (NEW)
│   ├── populate_levels.php (NEW)
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_programmes.php (FIXED)
│   ├── edit_programmes.php (FIXED)
│   ├── delete_programmes.php (FIXED)
│   ├── manage_modules.php (FIXED)
│   ├── edit_modules.php
│   ├── delete_modules.php (FIXED)
│   ├── manage_staff.php
│   ├── edit_staff.php (FIXED)
│   ├── delete_staff.php (FIXED)
│   └── manage_interests.php
├── config/
│   └── db.php
├── css/
│   └── university.css (FIXED)
├── includes/
│   ├── header.php
│   ├── footer.php (FIXED)
│   ├── admin_header.php (FIXED)
│   └── admin_footer.php
├── public/
│   ├── index.php (FIXED)
│   ├── programme.php (FIXED)
│   ├── register_interest.php
│   ├── contact.php (FIXED)
│   ├── staff.php
│   ├── accessibility.php
│   ├── privacy.php
│   └── open-days.php
├── staff/
│   ├── dashboard.php (FIXED)
│   ├── login.php
│   └── logout.php
├── uploads/ (auto-created)
├── schema.sql
└── SETUP_CHECKLIST.md (NEW)
```

## Security Considerations

- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS prevention (htmlspecialchars on all output)
- ✅ Password hashing (password_hash/password_verify)
- ✅ Session management for authentication
- ✅ File upload validation (type and size)
- ✅ Honeypot spam prevention on forms
- ⚠️ CSRF protection (recommended to add)
- ⚠️ Rate limiting (recommended to add)

## Performance Considerations

- ✅ Indexed foreign keys
- ✅ Efficient JOIN queries
- ⚠️ Consider adding caching for frequently accessed data
- ⚠️ Consider image optimization/resizing on upload

## Accessibility

- ✅ Semantic HTML structure
- ✅ ARIA labels and roles
- ✅ Keyboard navigation support
- ✅ Form labels and error messages
- ✅ Skip navigation links
- ✅ Alt text for images
- ⚠️ Screen reader testing recommended

## Next Steps

1. Run `admin/setup_database.php` immediately after importing schema
2. Create admin account via `admin/signup.php`
3. Populate levels table (done automatically by setup script)
4. Add staff members
5. Create programmes
6. Add modules and link to programmes
7. Test all functionality using the checklist above

## Support

If you encounter any issues:
1. Check PHP error logs
2. Verify database credentials in `config/db.php`
3. Ensure uploads directory has write permissions
4. Run `admin/setup_database.php` to verify database integrity
