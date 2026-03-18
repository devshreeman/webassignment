10/02/2025
Web Project Scenario
Student Course Hub
Your client is a university in the UK requiring an application to market a large
number of undergraduate and postgraduate degree programmes to prospective
students.
The purpose of this application is to collect contact details of prospective
students interested in specific degree programmes. This allows the university to
send targeted communications regarding events such as open days, application
deadlines, and programme updates—ultimately encouraging students to apply.
It is crucial that the university can generate a mailing list which indicates
exactly which programmes a student has expressed interest in.
Student-facing interface
The student-facing application should be a website providing access to details of
all available programmes. The website should be visually appealing, intuitive,
and easy to navigate.
For each programme, the system should display a list of modules for each year
of study. It should also show details of the staﬀ members involved (i.e.
programme leaders and module leaders).
Students should quickly find programme details and register their interest
eﬀortlessly.
The system must be mobile-friendly, ensuring seamless use on all devices.
Accessibility is a high priority—the site should support keyboard-only
navigation and comply with WCAG2 (Web Content Accessibility Guidelines).
Administration interface
The administration interface should allow authorized users to:
Create, update, and delete programmes and modules.
Publish/unpublish programmes to control what appears on the student-facing
website.
1 / 4
10/02/2025
Security & Data Protection
The system will handle personal data (student contact details), making security
a critical requirement. T o protect user information and prevent unauthorized
access, the following measures should be considered:
Administrator authentication – Access to administrative features should be
protected by login credentials.
Role-based access control – Only authorized administrators should be able to
manage programme data and view student mailing lists.
Prevent Cross-Site Scripting (XSS) – All user input should be validated and
sanitized before being stored or displayed to prevent malicious scripts from
running in the browser.
Indicative user stories
The following user stories serve as guidance. These user stories are optional,
but implementing at least some of them is essential for a functional application.
The student perspective
Students are primarily interested in discovering the available programmes,
reviewing their associated modules and registering their interest.
As a prospective student...
I want to... so that I can...
view a list of available undergraduate and postgraduate
programmes
explore my options.
see detailed descriptions of programmes
understand what each course
covers.
browse a list of modules for a programme
see what subjects I would
study.
see who teaches each module
learn about the faculty
members.
filter programmes by level (Undergraduate/Postgraduate) easily find courses relevant to
me.
see which modules are taught in each year of a programme
understand the structure of the
course.
search for programmes based on keywords (e.g.,
“Cyber Security”) find relevant courses quickly.
2 / 4
10/02/2025
I want to... so that I can...
see if a module is shared across multiple programmes
know which courses have
similar content.
see images associated with each programme and module get a visual representation of
what they involve.
register my interest in a programme
receive updates and further
details.
manage or withdraw my interest in a programme
avoid receiving unwanted
communication.
The administrator perspective
Administrators require full access to creating and updating programmes and
associate data.
As an administrator...
I want to... so that I can...
add new programmes to the database keep the system up to date.
update programme descriptions and images provide prospective students
with accurate information.
delete outdated programmes prevent student confusion.
add new modules to the database
ensure courses remain up to
date with new technologies.
assign or reassign a module leader to a module
ensure faculty responsibilities
are clear.
publish or unpublish a programme
create drafts and quickly
remove invalid information.
view a list of prospective students interested in a programme send them relevant updates.
export a mailing list of interested students send bulk emails easily.
remove invalid or duplicate interest registrations keep my mailing lists accurate.
The staﬀ perspective
You may also consider the staﬀ members perspective, the system might allow
for these user stories.
As a staﬀ member
I want to... so that I can...
3 / 4
10/02/2025
I want to... I want to see which modules I am leading
I want to see which programmes include the modules I teach so that I can...
know my teaching
responsibilities.
understand my impact across
courses.
Good Luck.
4 / 4