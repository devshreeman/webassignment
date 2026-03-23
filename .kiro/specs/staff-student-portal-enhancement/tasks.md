# Implementation Tasks: Staff & Student Portal Enhancement

**Feature Name:** Staff & Student Portal Enhancement  
**Spec Type:** Feature Enhancement  
**Created:** 2026-03-23  
**Status:** Ready for Implementation

---

## Task Overview

This document outlines the implementation tasks for enhancing the staff portal and creating a new student portal. Tasks are organized by phase and include database migrations, backend development, frontend development, and testing.

**Total Estimated Tasks:** 35 tasks across 5 phases

---

## Phase 1: Database Schema & Setup

### 1.1 Database Schema Updates
- [ ] Create `students` table with all required fields (StudentID, FullName, Email, Phone, Password, CreatedAt, UpdatedAt)
- [ ] Add `StudentID` column to `interestedstudents` table (INT, NULL, with foreign key to students.StudentID)
- [ ] Add foreign key constraint on `interestedstudents.StudentID` with ON DELETE SET NULL
- [ ] Add index on `interestedstudents.StudentID` for query performance
- [ ] Add `Phone` column to `staff` table (VARCHAR(20), NULL)
- [ ] Create database migration script in `admin/setup_database.php`
- [ ] Test migration script on clean database
- [ ] Verify all indexes are created correctly

### 1.2 Directory Structure Setup
- [ ] Create `students/` directory in project root
- [ ] Create `uploads/staff/` directory for staff photos
- [ ] Set proper permissions on upload directories (755)
- [ ] Verify upload directories are writable

---

## Phase 2: Student Portal - Authentication & Core

### 2.1 Student Registration
- [ ] Create `students/register.php` with registration form
- [ ] Implement form validation (name, email, phone, password, confirm password)
- [ ] Add email uniqueness check against students table
- [ ] Implement password hashing using `password_hash()`
- [ ] Create student record in database on successful registration
- [ ] Set `$_SESSION['student']` with StudentID, FullName, Email
- [ ] Implement session regeneration on successful registration
- [ ] Add link to login page for existing users
- [ ] Add link back to public site
- [ ] Test registration with valid and invalid data
- [ ] Test duplicate email prevention

### 2.2 Student Login
- [ ] Create `students/login.php` with login form
- [ ] Implement authentication logic (email and password verification)
- [ ] Use `password_verify()` for password checking
- [ ] Set `$_SESSION['student']` on successful login
- [ ] Implement session regeneration on successful login
- [ ] Add generic error message for failed login (don't reveal if email exists)
- [ ] Add link to registration page
- [ ] Add link back to public site
- [ ] Test login with valid credentials
- [ ] Test login with invalid credentials
- [ ] Test session persistence after login

### 2.3 Student Logout
- [ ] Create `students/logout.php`
- [ ] Implement session destruction logic
- [ ] Clear all session data
- [ ] Redirect to public homepage after logout
- [ ] Test logout functionality

### 2.4 Student Dashboard
- [ ] Create `students/dashboard.php` with authentication check
- [ ] Implement query to fetch student's programme interests
- [ ] Display welcome message with student name
- [ ] Create card/table layout for programme interests
- [ ] Show programme name, level, and registration date for each interest
- [ ] Add "Remove Interest" button for each programme
- [ ] Implement remove interest functionality (POST with _action=remove_interest)
- [ ] Verify student owns interest before deletion
- [ ] Add empty state when no interests registered
- [ ] Add "Browse Programmes" call-to-action link
- [ ] Add link to student settings page
- [ ] Test dashboard display with multiple interests
- [ ] Test dashboard empty state
- [ ] Test remove interest functionality

### 2.5 Student Settings/Profile
- [ ] Create `students/settings.php` with authentication check
- [ ] Display current profile information (name, email, phone)
- [ ] Create profile update form with pre-filled values
- [ ] Implement profile update logic (POST with _action=update_profile)
- [ ] Validate name, email, and phone on update
- [ ] Check email uniqueness (excluding current student)
- [ ] Update session data after profile change
- [ ] Create password change form
- [ ] Implement password change logic (POST with _action=change_password)
- [ ] Verify current password before allowing change
- [ ] Validate new password (min 8 characters)
- [ ] Verify password confirmation matches
- [ ] Display success/error messages
- [ ] Test profile update with valid data
- [ ] Test email uniqueness validation
- [ ] Test password change with correct/incorrect current password

---

## Phase 3: Staff Portal - Profile Management

### 3.1 Staff Settings/Profile
- [ ] Create `staff/settings.php` with authentication check
- [ ] Display current staff profile information (name, email, phone, photo)
- [ ] Create profile update form with pre-filled values
- [ ] Implement profile update logic (POST with _action=update_profile)
- [ ] Validate name, email, and phone on update
- [ ] Check email uniqueness in staff table (excluding current staff)
- [ ] Update session data after profile change
- [ ] Implement photo upload functionality
- [ ] Validate photo file type (JPG, PNG, GIF, WebP) using MIME detection
- [ ] Validate photo file size (max 5MB)
- [ ] Generate unique filename for uploaded photo
- [ ] Store photo in `uploads/staff/` directory
- [ ] Delete old photo when uploading new one
- [ ] Implement photo removal functionality
- [ ] Create password change form
- [ ] Implement password change logic (POST with _action=change_password)
- [ ] Verify current password before allowing change
- [ ] Validate new password (min 8 characters)
- [ ] Display success/error messages
- [ ] Test profile update with valid data
- [ ] Test photo upload with valid/invalid files
- [ ] Test photo removal
- [ ] Test password change functionality

### 3.2 Staff Interested Students View
- [ ] Create `staff/interested_students.php` with authentication check
- [ ] Implement query to fetch interested students for staff's modules
- [ ] Join interestedstudents → programmes → programmemodules → modules
- [ ] Filter by ModuleLeaderID = current staff ID
- [ ] Display student name, email, programme name, registration date
- [ ] Show total count of interested students
- [ ] Create programme filter dropdown
- [ ] Populate dropdown with programmes containing staff's modules
- [ ] Implement filter logic (GET parameter ?prog={ProgrammeID})
- [ ] Update results based on selected filter
- [ ] Persist filter selection during session
- [ ] Add "Clear Filter" option
- [ ] Implement CSV export functionality (GET parameter ?export=1)
- [ ] Generate CSV with headers: Name, Email, Programme, Registration Date
- [ ] Set proper CSV headers (Content-Type, Content-Disposition)
- [ ] Include UTF-8 BOM for Excel compatibility
- [ ] Respect current filter in CSV export
- [ ] Add empty state when no interested students
- [ ] Implement pagination if more than 50 students
- [ ] Test view with multiple interested students
- [ ] Test filtering by programme
- [ ] Test CSV export
- [ ] Test empty state for staff with no modules

### 3.3 Staff Dashboard Enhancement
- [ ] Update `staff/dashboard.php` to add link to interested students page
- [ ] Add "Interested Students" card or navigation item
- [ ] Display count of interested students on dashboard (optional)
- [ ] Add link to staff settings page
- [ ] Test navigation to new pages

---

## Phase 4: Integration & Navigation Updates

### 4.1 Public Header Updates
- [ ] Update `includes/header.php` to add student login option
- [ ] Modify login dropdown to show three options: Admin, Staff, Student
- [ ] Add logic to display student name when `$_SESSION['student']` is set
- [ ] Add student logout link when logged in
- [ ] Maintain existing admin and staff session display logic
- [ ] Test header display for all user types (guest, admin, staff, student)

### 4.2 Programme Interest Registration Enhancement
- [ ] Update `public/register_interest.php` to check for student session
- [ ] If logged in as student: auto-fill name and email from session
- [ ] If logged in as student: set StudentID in interestedstudents record
- [ ] If logged in as student: check duplicate using StudentID + ProgrammeID
- [ ] If not logged in: use existing anonymous registration logic
- [ ] Maintain backward compatibility with anonymous interests
- [ ] Test interest registration while logged in as student
- [ ] Test interest registration while not logged in
- [ ] Test duplicate prevention for logged-in students

### 4.3 Admin Interest Management Updates
- [ ] Update `admin/manage_interests.php` to show StudentID column (optional)
- [ ] Add indicator for interests linked to student accounts vs anonymous
- [ ] Test admin view displays both types of interests correctly

---

## Phase 5: Security, Validation & Testing

### 5.1 Security Implementation
- [ ] Implement CSRF token generation and validation for all forms
- [ ] Add CSRF token to student registration form
- [ ] Add CSRF token to student login form
- [ ] Add CSRF token to student settings forms
- [ ] Add CSRF token to staff settings forms
- [ ] Add CSRF token to interest removal form
- [ ] Validate CSRF tokens on all POST requests
- [ ] Implement session security configuration (httponly, secure, strict mode)
- [ ] Add session timeout check (2 hours inactivity)
- [ ] Implement session regeneration on login
- [ ] Add periodic session regeneration (every 30 minutes)
- [ ] Verify all database queries use prepared statements
- [ ] Verify all user input is escaped with htmlspecialchars() on output
- [ ] Test SQL injection attempts on all forms
- [ ] Test XSS attempts on all input fields
- [ ] Test CSRF protection on all forms

### 5.2 Input Validation
- [ ] Implement server-side email validation for all forms
- [ ] Implement name validation (2-100 chars, allowed characters)
- [ ] Implement phone validation (10-20 chars, digits and separators)
- [ ] Implement password validation (min 8 chars)
- [ ] Implement file upload validation (type, size, MIME)
- [ ] Add client-side validation hints (HTML5 attributes)
- [ ] Test validation with edge cases (empty, too long, special characters)
- [ ] Test validation error messages display correctly

### 5.3 Authorization Testing
- [ ] Test staff cannot access other staff profiles
- [ ] Test students cannot access other student data
- [ ] Test unauthenticated users are redirected to login
- [ ] Test staff can only view interested students for their modules
- [ ] Test students can only remove their own interests
- [ ] Test session-based access control for all protected pages

### 5.4 Functional Testing
- [ ] Test complete student registration flow
- [ ] Test complete student login flow
- [ ] Test student dashboard with 0, 1, and multiple interests
- [ ] Test student profile update flow
- [ ] Test student password change flow
- [ ] Test staff profile update flow
- [ ] Test staff photo upload and removal
- [ ] Test staff password change flow
- [ ] Test staff interested students view with filtering
- [ ] Test CSV export with different filters
- [ ] Test programme interest registration (logged in and anonymous)
- [ ] Test navigation between all new pages
- [ ] Test logout functionality for students and staff

### 5.5 UI/UX Testing
- [ ] Test responsive design on mobile devices
- [ ] Test responsive design on tablets
- [ ] Test responsive design on desktop
- [ ] Test form validation messages display correctly
- [ ] Test success messages display and auto-dismiss
- [ ] Test error messages display clearly
- [ ] Test empty states display correctly
- [ ] Test keyboard navigation works on all forms
- [ ] Test screen reader compatibility (basic ARIA)
- [ ] Verify color contrast meets WCAG 2.1 AA standards

### 5.6 Performance Testing
- [ ] Test page load times for all new pages
- [ ] Test query performance with large datasets (100+ interested students)
- [ ] Test pagination works correctly
- [ ] Test CSV export with large datasets
- [ ] Verify database indexes are being used (EXPLAIN queries)

### 5.7 Browser Compatibility Testing
- [ ] Test on Chrome (latest)
- [ ] Test on Firefox (latest)
- [ ] Test on Safari (latest)
- [ ] Test on Edge (latest)
- [ ] Test on mobile browsers (iOS Safari, Chrome Mobile)

---

## Phase 6: Documentation & Deployment

### 6.1 Documentation
- [ ] Update README with new features
- [ ] Document database schema changes
- [ ] Document new directory structure
- [ ] Create user guide for staff profile management
- [ ] Create user guide for student portal
- [ ] Document security considerations
- [ ] Update admin documentation for managing students

### 6.2 Deployment Preparation
- [ ] Create deployment checklist
- [ ] Prepare database migration script for production
- [ ] Verify upload directories exist and are writable
- [ ] Test migration script on staging environment
- [ ] Create rollback plan
- [ ] Prepare release notes

### 6.3 Post-Deployment
- [ ] Run database migration on production
- [ ] Verify all new pages are accessible
- [ ] Test critical user flows on production
- [ ] Monitor error logs for issues
- [ ] Collect user feedback
- [ ] Address any critical bugs

---

## Task Dependencies

### Critical Path:
1. Phase 1 (Database) must complete before all other phases
2. Phase 2 (Student Portal) can proceed after Phase 1
3. Phase 3 (Staff Portal) can proceed after Phase 1
4. Phase 4 (Integration) requires Phase 2 and Phase 3 to be complete
5. Phase 5 (Testing) should run throughout but final testing after Phase 4
6. Phase 6 (Documentation) can proceed in parallel with Phase 5

### Parallel Work Opportunities:
- Student Portal (Phase 2) and Staff Portal (Phase 3) can be developed in parallel
- Documentation (Phase 6.1) can be written while development is ongoing
- Security implementation (Phase 5.1) can be integrated throughout development

---

## Risk Mitigation

### High-Risk Tasks:
- Database migration (1.1) - Test thoroughly on staging first
- CSRF implementation (5.1) - May break existing forms if not careful
- Session management changes (5.1) - Could log out existing users

### Testing Priorities:
1. Authentication and authorization (security critical)
2. Data integrity (prevent data loss or corruption)
3. User experience (ensure smooth workflows)
4. Performance (ensure scalability)

---

## Success Criteria

### Phase Completion Criteria:
- **Phase 1:** Database schema updated, migration script tested
- **Phase 2:** Students can register, login, view dashboard, manage interests
- **Phase 3:** Staff can update profile, view interested students, export CSV
- **Phase 4:** All navigation updated, interest registration enhanced
- **Phase 5:** All security measures implemented, all tests passing
- **Phase 6:** Documentation complete, deployment successful

### Overall Success:
- All 35 tasks completed
- All user stories from requirements document satisfied
- All acceptance criteria met
- Zero critical security vulnerabilities
- WCAG 2.1 AA compliance maintained
- Positive user feedback from staff and students

---

**Document Status:** Ready for Implementation  
**Next Steps:** Begin Phase 1 - Database Schema & Setup
