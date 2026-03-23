# Requirements: Staff & Student Portal Enhancement

**Feature Name:** Staff & Student Portal Enhancement  
**Spec Type:** Feature Enhancement  
**Created:** 2026-03-23  
**Status:** Draft

---

## 1. Overview

### 1.1 Feature Summary
Enhance the existing staff portal and create a new student portal to enable self-service profile management and interest tracking. Staff members will be able to update their own details and view interested students for their programmes/modules. Students will be able to register accounts, manage their programme interests, and update their profiles.

### 1.2 Business Goals
- Reduce administrative overhead by allowing staff to manage their own profiles
- Provide staff with visibility into prospective student interest for their teaching areas
- Enable students to self-manage their programme interests without admin intervention
- Improve data accuracy through self-service updates
- Enhance user engagement with personalized portals

### 1.3 Success Criteria
- Staff can successfully update their profile information without admin assistance
- Staff can view and filter interested students by their programmes/modules
- Students can register, login, and manage their programme interests independently
- All profile updates are validated and stored securely
- No regression in existing admin functionality

---

## 2. User Stories

### 2.1 Staff Profile Management

**US-1: Update Personal Information**
- **As a** staff member
- **I want to** update my name, email, and contact number
- **So that** my information stays current without needing admin assistance

**Acceptance Criteria:**
- Staff can access a "My Profile" or "Settings" page from the staff dashboard
- Form displays current values pre-filled
- Name field is required and accepts 2-100 characters
- Email field is required, validated, and checks for uniqueness
- Contact number is optional and accepts phone number format
- Success message displays after successful update
- Email change requires re-authentication or confirmation
- Changes are immediately reflected in the system

**US-2: Update Profile Photo**
- **As a** staff member
- **I want to** upload or change my profile photo
- **So that** students and colleagues can recognize me

**Acceptance Criteria:**
- Staff can upload a new photo (JPG, PNG, GIF, WebP)
- Maximum file size is 5MB
- Current photo is displayed if one exists
- Option to remove current photo
- Photo is resized/optimized for display
- Old photo file is deleted when replaced
- Preview of uploaded photo before saving

**US-3: Change Password**
- **As a** staff member
- **I want to** change my password
- **So that** I can maintain account security

**Acceptance Criteria:**
- Requires current password verification
- New password must be at least 8 characters
- Password confirmation field must match
- Clear error messages for validation failures
- Success message after password change
- Session remains active after password change

### 2.2 Staff Interest Tracking

**US-4: View Interested Students**
- **As a** staff member
- **I want to** see a list of students interested in programmes containing my modules
- **So that** I can understand prospective student interest in my teaching areas

**Acceptance Criteria:**
- New "Interested Students" page accessible from staff dashboard
- Displays student name, email, programme name, and registration date
- Shows count of total interested students
- Data is read-only (staff cannot modify)
- Empty state message when no interested students exist
- Pagination if more than 50 students

**US-5: Filter Interested Students**
- **As a** staff member
- **I want to** filter interested students by specific programmes or modules
- **So that** I can focus on relevant prospective students

**Acceptance Criteria:**
- Dropdown filter for programmes containing staff's modules
- Filter by "All Programmes" or specific programme
- Results update immediately when filter changes
- Filter selection persists during session
- Count updates to reflect filtered results
- Clear filter option to return to all results

**US-6: Export Interested Students**
- **As a** staff member
- **I want to** export the interested students list to CSV
- **So that** I can use the data for outreach or analysis

**Acceptance Criteria:**
- "Export CSV" button visible on interested students page
- CSV includes: Name, Email, Programme, Registration Date
- Filename includes date: `interested_students_YYYY-MM-DD.csv`
- Export respects current filter selection
- Download initiates immediately on click

### 2.3 Student Portal

**US-7: Student Registration**
- **As a** prospective student
- **I want to** create an account
- **So that** I can manage my programme interests

**Acceptance Criteria:**
- Registration page accessible from public site
- Required fields: Full Name, Email, Password
- Optional fields: Phone Number
- Email must be unique in system
- Password must be at least 8 characters
- Password confirmation field required
- Email validation before account creation
- Success message and automatic login after registration
- Link to login page for existing users

**US-8: Student Login**
- **As a** registered student
- **I want to** log into my account
- **So that** I can access my dashboard and manage interests

**Acceptance Criteria:**
- Login page accessible from public site header
- Login with email and password
- "Remember me" option (optional)
- Clear error message for invalid credentials
- Link to registration page for new users
- Redirect to student dashboard after successful login
- Session management with secure cookies

**US-9: Student Dashboard**
- **As a** logged-in student
- **I want to** see my registered programme interests
- **So that** I can track what I've expressed interest in

**Acceptance Criteria:**
- Dashboard displays all programmes student has registered interest in
- Shows programme name, level, and registration date
- Option to remove interest from dashboard
- Link to view full programme details
- Empty state when no interests registered
- Call-to-action to browse programmes

**US-10: Manage Programme Interests**
- **As a** logged-in student
- **I want to** add or remove programme interests
- **So that** I can keep my interests current

**Acceptance Criteria:**
- "Register Interest" button on programme pages
- If logged in: adds interest immediately with confirmation
- If not logged in: prompts to login/register first
- Cannot register duplicate interest for same programme
- "Remove Interest" button on dashboard for each programme
- Confirmation dialog before removing interest
- Success message after add/remove action

**US-11: Update Student Profile**
- **As a** logged-in student
- **I want to** update my profile information
- **So that** my contact details stay current

**Acceptance Criteria:**
- "My Profile" or "Settings" page accessible from student dashboard
- Can update: Name, Email, Phone Number
- Can change password (requires current password)
- Email uniqueness validation
- Success message after update
- Changes reflected immediately in system

---

## 3. Functional Requirements

### 3.1 Database Schema Changes

**FR-1: Students Table**
- Create new `students` table with fields:
  - `StudentID` (INT, PRIMARY KEY, AUTO_INCREMENT)
  - `FullName` (VARCHAR(100), NOT NULL)
  - `Email` (VARCHAR(150), UNIQUE, NOT NULL)
  - `Phone` (VARCHAR(20), NULL)
  - `Password` (VARCHAR(255), NOT NULL)
  - `CreatedAt` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
  - `UpdatedAt` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**FR-2: InterestedStudents Table Modification**
- Add optional `StudentID` (INT, NULL, FOREIGN KEY to students.StudentID)
- Keep existing `StudentName` and `Email` for backward compatibility
- Add index on `StudentID` for performance

**FR-3: Staff Table Enhancement**
- Add `Phone` (VARCHAR(20), NULL) column if not exists
- Ensure `password` column exists (already added in previous migration)

### 3.2 Authentication & Authorization

**FR-4: Student Authentication**
- Implement student login system separate from staff/admin
- Use password hashing (bcrypt/password_hash)
- Session management with `$_SESSION['student']`
- Logout functionality

**FR-5: Staff Self-Service Authorization**
- Staff can only edit their own profile
- Staff can only view interested students for programmes containing their modules
- No access to other staff profiles or admin functions

**FR-6: Student Authorization**
- Students can only edit their own profile
- Students can only manage their own interests
- No access to other student data

### 3.3 Data Validation

**FR-7: Input Validation**
- All email addresses validated with `filter_var(FILTER_VALIDATE_EMAIL)`
- Names: 2-100 characters, no special characters except spaces, hyphens, apostrophes
- Phone numbers: optional, 10-20 characters, digits and common separators
- Passwords: minimum 8 characters, no maximum
- File uploads: validate type and size before processing

**FR-8: Duplicate Prevention**
- Email uniqueness enforced at database level (UNIQUE constraint)
- Check for duplicate student interest before insertion
- Handle duplicate errors gracefully with user-friendly messages

### 3.4 File Management

**FR-9: Photo Upload**
- Supported formats: JPG, PNG, GIF, WebP
- Maximum size: 5MB
- Store in `uploads/staff/` directory
- Generate unique filename: `staff_{timestamp}_{random}.{ext}`
- Delete old photo when uploading new one
- Validate file type server-side (not just extension)

### 3.5 Data Display

**FR-10: Staff Interested Students View**
- Query joins: `interestedstudents` → `programmes` → `programmemodules` → `modules`
- Filter by `ModuleLeaderID = current_staff_id`
- Display: Student Name, Email, Programme Name, Registration Date
- Sort by registration date (newest first)
- Pagination: 50 records per page

**FR-11: Student Dashboard View**
- Query: `interestedstudents` WHERE `StudentID = current_student_id`
- Join with `programmes` and `levels` for full details
- Display: Programme Name, Level, Registration Date, Actions
- Sort by registration date (newest first)

---

## 4. Non-Functional Requirements

### 4.1 Security

**NFR-1: Password Security**
- All passwords hashed using PHP `password_hash()` with PASSWORD_DEFAULT
- Never store or display plain-text passwords
- Password change requires current password verification

**NFR-2: Session Security**
- Use secure session configuration
- Regenerate session ID on login
- Clear session data on logout
- Session timeout after 2 hours of inactivity

**NFR-3: SQL Injection Prevention**
- All database queries use prepared statements with parameter binding
- No direct string concatenation in SQL queries

**NFR-4: XSS Prevention**
- All user input escaped with `htmlspecialchars()` before display
- Use `ENT_QUOTES` flag for attribute context

**NFR-5: CSRF Protection**
- Implement CSRF tokens for all state-changing forms
- Validate token on form submission

### 4.2 Performance

**NFR-6: Query Optimization**
- Add database indexes on frequently queried columns
- Use JOIN instead of multiple queries where possible
- Limit result sets with pagination

**NFR-7: File Upload Performance**
- Validate file size before processing
- Use efficient file operations
- Clean up old files to prevent disk bloat

### 4.3 Usability

**NFR-8: User Feedback**
- Display success messages for all successful actions
- Display clear error messages for validation failures
- Use consistent alert styling (success, error, info)
- Auto-dismiss success messages after 5 seconds

**NFR-9: Responsive Design**
- All new pages work on mobile, tablet, and desktop
- Forms are touch-friendly
- Tables are scrollable on small screens

**NFR-10: Accessibility**
- All forms have proper labels
- Error messages associated with form fields
- Keyboard navigation supported
- ARIA attributes where appropriate
- Maintain WCAG 2.1 AA compliance

### 4.4 Compatibility

**NFR-11: Browser Support**
- Support latest versions of Chrome, Firefox, Safari, Edge
- Graceful degradation for older browsers
- No JavaScript frameworks required (vanilla JS only)

**NFR-12: PHP Compatibility**
- Compatible with PHP 7.4+
- Use only standard PHP functions
- No external dependencies beyond PDO

---

## 5. User Interface Requirements

### 5.1 Staff Portal Pages

**UI-1: Staff Settings Page** (`staff/settings.php`)
- Similar layout to admin settings page
- Sections: My Profile, Change Password
- Form fields: Name, Email, Phone, Photo
- Current photo preview with remove option
- Save button for profile updates
- Separate form for password change

**UI-2: Staff Interested Students Page** (`staff/interested_students.php`)
- Page header with title and count
- Filter dropdown for programmes
- Export CSV button
- Data table with columns: Name, Email, Programme, Date
- Empty state when no students
- Pagination controls if needed

### 5.2 Student Portal Pages

**UI-3: Student Registration Page** (`students/register.php`)
- Clean, centered form layout
- Fields: Full Name, Email, Phone (optional), Password, Confirm Password
- Submit button: "Create Account"
- Link to login page
- Link back to public site

**UI-4: Student Login Page** (`students/login.php`)
- Clean, centered form layout
- Fields: Email, Password
- Submit button: "Sign In"
- Link to registration page
- Link back to public site

**UI-5: Student Dashboard** (`students/dashboard.php`)
- Welcome message with student name
- Section: "My Programme Interests"
- Cards or table showing registered programmes
- Each programme shows: Name, Level, Date, Remove button
- Empty state with "Browse Programmes" CTA
- Link to profile settings

**UI-6: Student Settings Page** (`students/settings.php`)
- Similar layout to staff settings
- Sections: My Profile, Change Password
- Form fields: Name, Email, Phone
- Save button for profile updates
- Separate form for password change

### 5.3 Navigation Updates

**UI-7: Public Header Updates**
- Add "Student Login" option to login dropdown
- Update dropdown to show: Admin Login, Staff Login, Student Login

**UI-8: Staff Dashboard Updates**
- Add "Interested Students" link to navigation/dashboard
- Add "My Settings" or "My Profile" link

**UI-9: Student Dashboard Navigation**
- Header with: Dashboard, Browse Programmes, My Settings, Logout
- Consistent with public site styling

---

## 6. Integration Points

### 6.1 Existing System Integration

**INT-1: Programme Interest Registration**
- Update `public/register_interest.php` to check if user is logged in
- If logged in as student: auto-fill name/email, link to StudentID
- If not logged in: show current form, create anonymous interest record

**INT-2: Staff Dashboard Enhancement**
- Add "Interested Students" card/link to existing `staff/dashboard.php`
- Show count of interested students

**INT-3: Admin Interest Management**
- Admin can still view all interested students
- Admin view shows whether interest is linked to student account

### 6.2 Email Notifications (Future Enhancement)
- Placeholder for future email notification system
- Not required for initial implementation

---

## 7. Data Migration

### 7.1 Existing Data Handling

**MIG-1: InterestedStudents Table**
- Existing records remain with NULL StudentID
- New registrations from logged-in students populate StudentID
- Both types of records coexist

**MIG-2: Staff Table**
- Add Phone column with NULL default
- Existing staff records unaffected

---

## 8. Testing Requirements

### 8.1 Functional Testing

**TEST-1: Staff Profile Update**
- Test successful profile update
- Test email uniqueness validation
- Test photo upload and removal
- Test password change with correct/incorrect current password

**TEST-2: Staff Interested Students**
- Test viewing students for staff with modules
- Test filtering by programme
- Test CSV export
- Test empty state for staff with no modules

**TEST-3: Student Registration & Login**
- Test successful registration
- Test duplicate email prevention
- Test login with valid/invalid credentials
- Test session management

**TEST-4: Student Interest Management**
- Test adding interest while logged in
- Test removing interest
- Test duplicate interest prevention
- Test viewing interests on dashboard

**TEST-5: Student Profile Update**
- Test successful profile update
- Test email uniqueness validation
- Test password change

### 8.2 Security Testing

**TEST-6: Authorization**
- Test staff cannot access other staff profiles
- Test students cannot access other student data
- Test unauthenticated access redirects to login

**TEST-7: Input Validation**
- Test SQL injection attempts
- Test XSS attempts
- Test file upload validation
- Test CSRF protection

### 8.3 Usability Testing

**TEST-8: User Experience**
- Test form validation messages
- Test success/error message display
- Test navigation flow
- Test mobile responsiveness

---

## 9. Constraints & Assumptions

### 9.1 Technical Constraints
- Must use existing tech stack: PHP, MySQL, vanilla JavaScript, custom CSS
- No external frameworks or libraries allowed
- Must maintain existing admin and staff functionality

### 9.2 Assumptions
- Staff members already have accounts created by admin
- Students will self-register (no admin approval required)
- Email addresses are unique across the system
- File upload directory is writable
- Database supports required schema changes

### 9.3 Out of Scope
- Email verification for student registration
- Password reset functionality
- Two-factor authentication
- Email notifications
- Student profile photos
- Social login integration

---

## 10. Risks & Mitigation

### 10.1 Risks

**RISK-1: Email Conflicts**
- Risk: Student email might conflict with existing interested student email
- Mitigation: Check for conflicts during registration, provide clear error message

**RISK-2: Data Integrity**
- Risk: Orphaned records if student account deleted
- Mitigation: Use foreign key with ON DELETE SET NULL

**RISK-3: Performance**
- Risk: Large number of interested students could slow queries
- Mitigation: Implement pagination, add database indexes

**RISK-4: Security**
- Risk: Weak passwords or session hijacking
- Mitigation: Enforce password requirements, use secure session configuration

### 10.2 Dependencies
- Database must support required schema changes
- PHP version must support password_hash function
- Server must allow file uploads

---

## 11. Success Metrics

### 11.1 Adoption Metrics
- 80%+ of staff update their profile within first month
- 50%+ of new interested students create accounts
- 90%+ of logged-in students manage interests via dashboard

### 11.2 Performance Metrics
- Page load time < 2 seconds for all new pages
- Zero SQL injection or XSS vulnerabilities
- 99%+ uptime for authentication system

### 11.3 User Satisfaction
- Positive feedback from staff on self-service capabilities
- Reduced admin support tickets for profile updates
- Increased student engagement with programme interests

---

## 12. Glossary

- **Staff Member**: Academic staff who teach modules and lead programmes
- **Student**: Prospective student interested in programmes
- **Programme Interest**: Record of student expressing interest in a programme
- **Module Leader**: Staff member responsible for teaching a specific module
- **Programme Leader**: Staff member responsible for a degree programme
- **Self-Service**: User ability to update own data without admin intervention

---

**Document Status:** Ready for Review  
**Next Steps:** Design phase - create technical design document
