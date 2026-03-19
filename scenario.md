10/02/2025

# Web Project Scenario

## Student Course Hub


Your client is a university in the UK requiring an application to market a large
number of undergraduate and postgraduate degree programmes to prospective
students.


The purpose of this application is to collect contact details of prospective
students interested in specific degree programmes. This allows the university to
send targeted communications regarding events such as open days, application
deadlines, and programme updates—ultimately encouraging students to apply.


It is crucial that the university can generate a mailing list which indicates
exactly which programmes a student has expressed interest in.

## Student-facing interface


The student-facing application should be a website providing access to details of
all available programmes. The website should be visually appealing, intuitive,
and easy to navigate.


For each programme, the system should display a list of modules for each year
of study. It should also show details of the staff members involved (i.e.
programme leaders and module leaders).


Students should quickly find programme details and register their interest
effortlessly.


The system must be mobile-friendly, ensuring seamless use on all devices.


Accessibility is a high priority—the site should support keyboard-only
[navigation and comply with WCAG2 (Web Content Accessibility Guidelines).](https://www.w3.org/WAI/WCAG22/quickref/?currentsidebar=%23col_overview&showtechniques=pageinfo%2C111&levels=aa%2Caaa&techniques=advisory%2Cfailures&technologies=js%2Cserver%2Csmil%2Cpdf#hiddensc)

## Administration interface


The administration interface should allow authorized users to:


Create, update, and delete programmes and modules.


Publish/unpublish programmes to control what appears on the student-facing
website.


1 / 4


10/02/2025

## Security & Data Protection


The system will handle personal data (student contact details), making security
a critical requirement. To protect user information and prevent unauthorized
access, the following measures should be considered:


Administrator authentication – Access to administrative features should be
protected by login credentials.


Role-based access control – Only authorized administrators should be able to
manage programme data and view student mailing lists.


Prevent Cross-Site Scripting (XSS) – All user input should be validated and
sanitized before being stored or displayed to prevent malicious scripts from
running in the browser.

## Indicative user stories


The following user stories serve as guidance. These user stories are optional,
but implementing at least some of them is essential for a functional application.

### The student perspective


Students are primarily interested in discovering the available programmes,
reviewing their associated modules and registering their interest.


As a prospective student...


**I want to...** **so that I can...**


view a list of available undergraduate and postgraduate
explore my options.
programmes


understand what each course
see detailed descriptions of programmes
covers.


see what subjects I would
browse a list of modules for a programme
study.


learn about the faculty
see who teaches each module
members.


easily find courses relevant to
filter programmes by level (Undergraduate/Postgraduate)
me.


understand the structure of the
see which modules are taught in each year of a programme
course.


search for programmes based on keywords (e.g., “Cyber Security”) find relevant courses quickly.


2 / 4


10/02/2025


**I want to...** **so that I can...**


know which courses have
see if a module is shared across multiple programmes
similar content.


get a visual representation of
see images associated with each programme and module
what they involve.


receive updates and further
register my interest in a programme
details.


avoid receiving unwanted
manage or withdraw my interest in a programme
communication.

### The administrator perspective


Administrators require full access to creating and updating programmes and
associate data.


As an administrator...


**I want to...** **so that I can...**


add new programmes to the database keep the system up to date.


provide prospective students
update programme descriptions and images
with accurate information.


delete outdated programmes prevent student confusion.


ensure courses remain up to
add new modules to the database
date with new technologies.


ensure faculty responsibilities
assign or reassign a module leader to a module
are clear.


create drafts and quickly
publish or unpublish a programme
remove invalid information.


view a list of prospective students interested in a programme send them relevant updates.


export a mailing list of interested students send bulk emails easily.


remove invalid or duplicate interest registrations keep my mailing lists accurate.

### The staff perspective


You may also consider the staff members perspective, the system might allow
for these user stories.


As a staff member


**I want to...** **so that I can...**


3 / 4


10/02/2025


**I want to...** **so that I can...**


know my teaching
I want to see which modules I am leading
responsibilities.


understand my impact across
I want to see which programmes include the modules I teach
courses.


Good Luck.


4 / 4


