---
title: Student Course Hub - University Programme Marketing System
status: in_progress
created: 2026-03-19
---

# Student Course Hub

## Overview

A web application for a UK university to market undergraduate and postgraduate degree programmes to prospective students. The system collects contact details of interested students and enables targeted communications about open days, application deadlines, and programme updates.

## Project Context

### Current State
- PHP-based web application with MySQL database
- Three main user interfaces: Public (students), Admin, and Staff
- Database schema with 7 tables: programmes, modules, staff, interestedstudents, programmemodules, levels, admin
- Basic CRUD operations implemented for programmes, modules, and staff
- Student interest registration functionality
- Image upload support for staff profiles

### Technology Stack
- Backend: PHP with PDO
- Database: MySQL/MariaDB
- Frontend: HTML, CSS (multiple stylesheets), vanilla JavaScript
- Server: Apache/XAMPP (localhost)

## Core Requirements

### 1. Student-Facing Interface
- Visually appealing, intuitive website
- Browse all available programmes with filtering (by level) and search
- View programme details including modules by year
- See staff members (programme leaders, module leaders)
- Register interest in programmes
- Mobile-friendly responsive design
- WCAG 2 accessibility compliance (keyboard navigation, screen readers)

### 2. Administration Interface
- Secure login system for administrators
- Create, update, delete programmes and modules
- Manage staff profiles
- Publish/unpublish programmes and modules
- View and export mailing lists of interested students
- Manage student interest registrations

### 3. Staff Interface (Optional Enhancement)
- View modules they are leading
- See which programmes include their modules
- Understand teaching responsibilities

### 4. Security & Data Protection
- Administrator authentication
- Role-based access control
- XSS prevention (input validation and sanitization)
- Secure password storage (hashing)
- Protection of personal data (GDPR considerations)

## Database Schema

### Tables
- `programmes`: Programme details, level, leader, duration, description, image, publish status
- `modules`: Module details, leader, description, image, publish status
- `staff`: Staff profiles with name, email, bio, photo
- `interestedstudents`: Student contact details and programme interests
- `programmemodules`: Junction table linking programmes to modules by year
- `levels`: Programme levels (Undergraduate/Postgraduate)
- `admin`: Administrator accounts

## User Stories

### Student Perspective
- [x] View list of available programmes
- [x] See detailed programme descriptions
- [x] Browse modules for each programme
- [x] See module leaders and programme leaders
- [x] Filter programmes by level
- [x] Search programmes by keywords
- [x] Register interest in programmes
- [ ] See which modules are shared across programmes
- [ ] View images for programmes and modules
- [ ] Manage/withdraw interest registrations

### Administrator Perspective
- [x] Add new programmes
- [x] Update programme descriptions and images
- [x] Delete programmes
- [x] Add new modules
- [x] Assign module leaders
- [x] Publish/unpublish programmes
- [ ] Publish/unpublish modules
- [ ] View interested students list
- [ ] Export mailing lists
- [ ] Remove invalid interest registrations
- [ ] Bulk operations for data management

### Staff Perspective
- [ ] View modules they are leading
- [ ] See programmes that include their modules
- [ ] Update their own profile information

## Tasks

### Task 1: Code Quality & Security Audit
**Status**: pending
**Priority**: high
**Description**: Review existing codebase for security vulnerabilities and code quality issues

**Acceptance Criteria**:
- [ ] Audit all user input handling for XSS vulnerabilities
- [ ] Verify SQL injection prevention (PDO prepared statements)
- [ ] Check password hashing implementation
- [ ] Review session management and authentication
- [ ] Identify any hardcoded credentials or sensitive data
- [ ] Check file upload security (staff photos)
- [ ] Document security findings and recommendations

---

### Task 2: Accessibility Compliance Review
**Status**: pending
**Priority**: high
**Description**: Audit and improve WCAG 2 AA compliance across all interfaces

**Acceptance Criteria**:
- [ ] Keyboard navigation testing on all pages
- [ ] Screen reader compatibility testing
- [ ] Proper ARIA labels and semantic HTML
- [ ] Color contrast compliance
- [ ] Form accessibility (labels, error messages)
- [ ] Skip navigation links
- [ ] Focus indicators visible
- [ ] Alt text for all images
- [ ] Document accessibility improvements needed

---

### Task 3: Mobile Responsiveness Enhancement
**Status**: pending
**Priority**: medium
**Description**: Ensure seamless mobile experience across all devices

**Acceptance Criteria**:
- [ ] Test on mobile devices (iOS, Android)
- [ ] Responsive navigation menu
- [ ] Touch-friendly interface elements
- [ ] Optimized images for mobile
- [ ] Fast loading times on mobile networks
- [ ] Proper viewport configuration
- [ ] Test on tablets and various screen sizes

---

### Task 4: Student Interest Management
**Status**: pending
**Priority**: medium
**Description**: Complete student interest registration and management features

**Acceptance Criteria**:
- [ ] Prevent duplicate interest registrations (same email + programme)
- [ ] Email validation on registration
- [ ] Confirmation message after registration
- [ ] Allow students to withdraw interest
- [ ] Admin view of interested students per programme
- [ ] Export mailing list functionality (CSV/Excel)
- [ ] Filter and search interested students
- [ ] Delete invalid registrations

---

### Task 5: Module Publishing System
**Status**: pending
**Priority**: medium
**Description**: Implement publish/unpublish functionality for modules

**Acceptance Criteria**:
- [ ] Add publish/unpublish toggle in admin module management
- [ ] Only show published modules on student-facing pages
- [ ] Cascade logic: unpublished modules don't appear in programmes
- [ ] Visual indicator of publish status in admin interface
- [ ] Bulk publish/unpublish operations

---

### Task 6: Staff Portal Implementation
**Status**: pending
**Priority**: low
**Description**: Create staff member portal for viewing teaching responsibilities

**Acceptance Criteria**:
- [ ] Staff login system
- [ ] Dashboard showing modules they lead
- [ ] List of programmes containing their modules
- [ ] Profile editing capability
- [ ] Secure authentication separate from admin

---

### Task 7: Enhanced Search & Filtering
**Status**: pending
**Priority**: medium
**Description**: Improve programme discovery with advanced search and filters

**Acceptance Criteria**:
- [ ] Search across programme names, descriptions, and modules
- [ ] Filter by multiple criteria simultaneously
- [ ] Sort options (alphabetical, duration, level)
- [ ] Search result highlighting
- [ ] "No results" state with suggestions
- [ ] Search performance optimization

---

### Task 8: Image Management System
**Status**: pending
**Priority**: low
**Description**: Improve image handling for programmes and modules

**Acceptance Criteria**:
- [ ] Image upload for programmes
- [ ] Image upload for modules
- [ ] Image validation (file type, size)
- [ ] Image optimization/resizing
- [ ] Default fallback images
- [ ] Delete old images when updating
- [ ] Secure file storage

---

### Task 9: Admin Dashboard Analytics
**Status**: pending
**Priority**: low
**Description**: Create analytics dashboard for administrators

**Acceptance Criteria**:
- [ ] Total programmes, modules, staff counts
- [ ] Interest registrations over time
- [ ] Most popular programmes
- [ ] Recent activity feed
- [ ] Quick action buttons
- [ ] Visual charts/graphs

---

### Task 10: Email Notification System
**Status**: pending
**Priority**: low
**Description**: Automated email notifications for interested students

**Acceptance Criteria**:
- [ ] Welcome email on interest registration
- [ ] Email templates for different events
- [ ] Unsubscribe functionality
- [ ] Email sending configuration
- [ ] Track email delivery status
- [ ] Bulk email capability for admins

---

### Task 11: Data Validation & Error Handling
**Status**: pending
**Priority**: high
**Description**: Comprehensive input validation and user-friendly error messages

**Acceptance Criteria**:
- [ ] Client-side validation for all forms
- [ ] Server-side validation for all inputs
- [ ] User-friendly error messages
- [ ] Validation feedback in real-time
- [ ] Prevent invalid data entry
- [ ] Graceful error handling for database errors
- [ ] Logging system for errors

---

### Task 12: Documentation & Deployment Guide
**Status**: pending
**Priority**: medium
**Description**: Create comprehensive documentation for deployment and maintenance

**Acceptance Criteria**:
- [ ] Installation guide
- [ ] Database setup instructions
- [ ] Configuration documentation
- [ ] User manuals (admin, staff, student)
- [ ] API documentation (if applicable)
- [ ] Troubleshooting guide
- [ ] Backup and recovery procedures

## Technical Debt & Improvements

### Code Organization
- Multiple CSS files (style.css, style1-5.css, university.css) - consolidate and organize
- Inconsistent naming conventions across files
- Duplicate code in admin CRUD operations
- Missing error handling in several files

### Database
- Consider adding indexes for frequently queried fields
- Add created_at/updated_at timestamps to all tables
- Implement soft deletes for programmes/modules
- Add unique constraints where appropriate

### Security Enhancements
- Implement CSRF protection
- Add rate limiting for login attempts
- Secure session configuration
- Environment-based configuration (remove hardcoded DB credentials)
- Implement proper logging and audit trails

### Performance
- Implement caching for frequently accessed data
- Optimize database queries (reduce N+1 queries)
- Lazy loading for images
- Minify CSS/JS for production

## Testing Strategy

### Manual Testing Checklist
- [ ] All user flows (student, admin, staff)
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile device testing
- [ ] Accessibility testing with screen readers
- [ ] Security testing (XSS, SQL injection attempts)
- [ ] Performance testing under load

### Automated Testing (Future)
- Unit tests for core business logic
- Integration tests for database operations
- End-to-end tests for critical user flows

## Deployment Considerations

### Production Requirements
- PHP 7.4+ (preferably 8.x)
- MySQL 5.7+ or MariaDB 10.4+
- HTTPS/SSL certificate
- Proper file permissions for uploads directory
- Environment variables for sensitive configuration
- Regular database backups
- Error logging configuration

### Environment Configuration
- Development, staging, and production environments
- Separate database credentials per environment
- Debug mode disabled in production
- Proper error reporting configuration

## Future Enhancements

- Multi-language support
- Programme comparison feature
- Virtual tour integration
- Student testimonials
- Application tracking system
- Integration with university CRM
- API for mobile app
- Advanced analytics and reporting
- Social media integration
- Live chat support

## Notes

- Current implementation uses localhost MySQL with root user (not production-ready)
- Multiple debug/migration files in admin folder should be removed for production
- Consider implementing a proper migration system for database changes
- Review and consolidate the multiple CSS files
- Implement proper environment configuration management
