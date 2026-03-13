# Academic Tracker System Specification

## 1. Purpose

This document describes the Academic Tracker system as a product and domain specification, not as a codebase or framework implementation. It is intended to be sufficient to recreate the current web system from scratch without referring to any other source.

This specification covers the interactive web application only.

This specification explicitly excludes all API-route behavior.

## 2. System Summary

Academic Tracker is a role-based academic operations system for managing:

- academic users
- student records
- teacher records
- parent records
- classes
- enrollments
- assignments and attendance
- grades and marks
- academic terms
- reference catalogs such as grades, subjects, and activity types

The system supports four person types:

- admin
- teacher
- student
- parent

The active product experience is centered around:

- administrators maintaining the master data and user records
- teachers managing classes, enrollments, activities, and student results
- students viewing their own classes and assignment outcomes

Parent records exist and can be linked to students, but there is no dedicated parent-facing web portal in the current web scope.

## 3. Product Goals

The system must enable an institution to:

- create and maintain academic users
- represent each user as a typed profile
- organize teaching into classes linked to a grade and a subject
- assign one active teacher to a class at a time
- enroll many students into a class
- copy or move a whole class roster into one or more other classes
- create class activities such as homework, attendance, and study material
- automatically initialize a per-student tracking record for each activity
- record marks or attendance outcomes for each enrolled student
- let students view only their own classes and assignment outcomes
- let staff inspect student and parent relationships
- provide summary analytics for class distribution and recent attendance absences

## 4. Primary Roles

### 4.1 Admin

Admin can:

- access the main dashboard
- manage all users
- manage grades
- manage subjects
- manage terms
- manage activity types
- view teachers
- view students
- view parents
- manage classes
- change their own password using the in-app password change page

### 4.2 Teacher

Teacher can:

- access the main dashboard
- view students
- view parents
- manage classes
- manage class activities
- log marks and attendance
- change their own password using the in-app password change page

Teacher cannot access the admin-only settings area.

### 4.3 Student

Student can:

- access a student-specific dashboard
- view only their own enrolled classes
- open a class and view their own assignment, study-material, and attendance outcomes

Student cannot access staff dashboards, management modules, or settings.

### 4.4 Parent

Parent exists as a valid user/profile type and can be created as a person record.

In the current web product, parent does not have a dedicated authenticated navigation area or parent-only pages.

## 5. Access Model

### 5.1 Authentication

- The system requires login for all operational pages.
- Public users can access the landing page.
- Registration is disabled.
- Password reset is enabled.
- Logout is available.

### 5.2 Post-login Routing

- Student users land on the student dashboard.
- Teacher and admin users land on the staff dashboard.

### 5.3 Authorization

Authorization is role-based and route/page-level.

- admin: full staff access, plus settings and teacher management
- teacher: staff access except admin-only settings and teacher management
- student: student-only dashboard and student assignments
- parent: no dedicated web area in current scope

### 5.4 Typed Route Binding

When a page expects a student, teacher, or parent entity, the referenced profile must match that type. A teacher page cannot be opened with a student profile id, and so on.

## 6. Navigation Map

### 6.1 Public Navigation

- Landing page
- Login
- Password reset request
- Password reset completion

### 6.2 Staff Navigation

- Dashboard
- Students
- Parents
- Teachers (admin only)
- Classes
- Settings
- Change Password

Settings contains:

- Activity Types
- Grades
- Subjects
- Terms
- Users

### 6.3 Student Navigation

- My Dashboard
- Per-class assignment view

## 7. Canonical Screens and Behaviors

### 7.1 Landing Page

Purpose:

- public entry page before authentication

Minimum behavior:

- presents system identity
- links users to sign in

### 7.2 Login and Password Recovery

Purpose:

- authenticate existing users
- support forgotten-password recovery

Rules:

- self-registration is not available
- authenticated users should not be shown the guest-only login experience again

### 7.3 Staff Dashboard

Audience:

- admin
- teacher

The dashboard must show:

- total active student count
- total active teacher count
- class distribution by active enrolled students
- recent student list
- attendance absences trend for the last five days
- comparison of current absence count versus the prior comparison window

Absence analytics rule:

- absence is derived from attendance activity results where the stored result represents the false state
- the current system encodes attendance false as score `1` and true as score `2`

Recent student list rule:

- show the most recently created active student profiles

Class distribution rule:

- calculate using active classes and each class's count of active student enrollments

### 7.4 Students List

Audience:

- admin
- teacher

Capabilities:

- searchable list of students
- paginated results
- adjustable page size
- open student detail page
- activate or deactivate a student by toggling the student's profile status through the linked user record
- shortcut to the user-management area for record creation/editing

Search should match at least:

- first name
- last name
- phone number
- identification number
- date of birth
- sex

### 7.5 Student Detail

Audience:

- admin
- teacher

The page must show:

- student identity and contact information
- current class memberships
- per-class status/actions

Actions:

- enroll the student into one or more classes
- un-enroll the student from a class by deactivating the enrollment record
- open the student's activity history for a selected class

Enrollment behavior:

- if an enrollment already exists but is inactive, reactivate it instead of creating a duplicate
- if an enrollment does not exist, create it

### 7.6 Student Activity History for Staff

Audience:

- admin
- teacher

The page must group activity results by activity type using tabbed or otherwise segmented navigation.

Supported activity type presentations:

- value activity: show activity name, note, mark, total, optional file, due date
- boolean activity: show activity name, note, boolean result label, optional file, due date
- static activity: show activity name, note, optional file, due date

The current web scope does not complete the "View Class" action from this page; it is treated as non-critical or unfinished behavior.

### 7.7 Parents List

Audience:

- admin
- teacher

Capabilities:

- list active parent profiles
- open parent detail page

### 7.8 Parent Detail

Audience:

- admin
- teacher

The page must show:

- parent identity and contact information
- currently linked students

Actions:

- link one or more students to the parent
- remove a parent-student link by deactivating the relationship
- open a linked student's detail page

Relationship behavior:

- if a relationship already exists and is active, reject the duplicate with an error
- if a relationship already exists and is inactive, reactivate it
- if no relationship exists, create it

### 7.9 Teachers List

Audience:

- admin only

Capabilities:

- searchable or browsable teacher list
- open teacher detail page

### 7.10 Teacher Detail

Audience:

- admin only

The page must show:

- teacher identity and contact information
- all active classes assigned to the teacher
- student counts per class

Action:

- open a class detail page

### 7.11 Classes List

Audience:

- admin
- teacher

Capabilities:

- searchable list of classes
- paginated results
- adjustable page size
- create class
- edit class
- activate or deactivate class
- open class detail page

Each class row must display:

- class name
- grade
- subject
- active teacher, if assigned
- number of active enrolled students
- active/inactive state

Search should match at least:

- class name
- teacher name
- subject name
- grade name

### 7.12 Class Detail

Audience:

- admin
- teacher

The page must show:

- class identity
- assigned teacher
- grade
- subject
- roster of active students

Actions:

- enroll multiple students into the class
- un-enroll a student from the class
- copy the current active roster into one or more other classes
- move the current active roster into one or more other classes
- open the class activity area
- open a selected student's activity history for this class

Copy behavior:

- duplicate the current active roster into the selected destination classes
- if a destination enrollment exists but is inactive, reactivate it
- do not create duplicate active enrollments

Move behavior:

- perform the copy behavior first
- then deactivate all active enrollments in the source class

### 7.13 Class Activities List

Audience:

- admin
- teacher

Purpose:

- manage all activities belonging to a class

The page must:

- group activities by activity type
- allow creation of a new activity
- show all activities for the class, including inactive ones if present in storage
- show download access to teacher-provided activity files
- allow opening a log-entry page for non-static activity types

Each activity row should show:

- name
- note
- total marks where relevant
- optional file attachment
- due date
- creation date
- active/inactive status

Current-state note:

- an edit button is shown in the current web product but no completed edit behavior is implemented for class activities

### 7.14 Activity Creation

Audience:

- admin
- teacher

Required fields:

- course
- teacher
- activity name
- activity type

Optional fields:

- note
- total marks
- due date
- file attachment

Critical rule:

- a new activity can be created only if there is an active term whose start date is on or before today and end date is on or after today

After successful creation:

- create one activity log record for every active student enrolled in the class at that moment
- initialize each log with no score, default incomplete status, and active state

### 7.15 Activity Log Entry Page

Audience:

- admin
- teacher

This page is used to record per-student outcomes for a selected activity.

The page must show:

- activity metadata
- list of active students in the class
- previously recorded outcomes where present
- input controls appropriate for the activity type

Logging rules by type:

- value activity: enter numeric marks per student
- boolean activity: record true/false per student using checkbox or equivalent selector
- static activity: no staff log-entry page is needed because it has no evaluative student result

Validation rules:

- for value activities, an entered mark cannot exceed the activity total
- for boolean activities, students explicitly checked are stored as the true state and all remaining students are stored as the false state

Faithful storage behavior of the current system:

- boolean false is stored as score `1`
- boolean true is stored as score `2`

Update behavior:

- if a log already exists for that student and activity, update it
- otherwise create it

### 7.16 Student Dashboard

Audience:

- student only

The page must show:

- the student's own profile summary
- the student's active class enrollments

Each class entry must show:

- class name
- grade
- subject
- action to open the student's assignment/results page for that class

### 7.17 Student Assignments Page

Audience:

- student only

Access rule:

- a student may only open this page for a class in which they are actively enrolled

The page must group results by activity type.

For value activities, show:

- assignment name
- note
- student's mark
- total marks
- percentage where both mark and total exist
- optional file
- due date

For boolean activities, show:

- assignment name
- note
- badge or status label using the configured true/false labels
- optional file
- due date

For static activities, show:

- assignment name
- note
- optional file
- due date

Current-state nuance:

- this page displays the file attached to the student's activity log record, not the teacher's activity file
- the web scope currently does not provide a student submission/upload workflow, so the file is typically blank unless populated by some external or future flow

### 7.18 Settings: Users

Audience:

- admin only

Capabilities:

- searchable user list
- paginated results
- adjustable page size
- create user
- edit user
- activate or deactivate user by toggling profile active state

User creation fields:

- full name
- email
- phone number
- identification number
- sex
- date of birth
- role

Creation rules:

- create a user account
- create a matching typed profile based on the selected role
- split the full name into first name and last name using the final space character
- set the initial password to a hash of the email address for manually created users

Edit rules:

- update user name and email
- reset password to a hash of the updated email address
- update the linked profile fields

Important fidelity note:

- resetting the password to the user's email during edit is current system behavior and should be preserved only if strict recreation is required

### 7.19 Settings: Grades

Audience:

- admin only

Capabilities:

- list grades
- create grade
- edit grade name
- activate or deactivate grade

### 7.20 Settings: Subjects

Audience:

- admin only

Capabilities:

- list subjects
- create subject
- edit subject name
- activate or deactivate subject

### 7.21 Settings: Terms

Audience:

- admin only

Capabilities:

- list terms
- create term
- edit term
- activate or deactivate term

Fields:

- name
- start date
- end date
- active/inactive flag

### 7.22 Settings: Activity Types

Audience:

- admin only

Capabilities:

- list activity types
- create activity type
- edit name, description, and optional image
- activate or deactivate activity type

Fields:

- name
- description
- type
- image
- true label
- false label
- active/inactive flag

Valid type values:

- boolean
- value
- static

Rules:

- image is required on creation
- true and false labels are used only for boolean activity types

### 7.23 Change Password

Audience:

- admin
- teacher

Rules:

- old password is required
- new password is required
- new password must differ from old password
- new password and confirmation must match
- old password must verify against the currently authenticated user

On success:

- update password
- redirect to the staff dashboard

## 8. Domain Model

The system assumes a relational data model.

### 8.1 User

Purpose:

- authentication record

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | full display name |
| email | text | yes | unique |
| password | text | yes | stored securely |
| role_id | identifier | yes | logical foreign key to Role |
| email_verified_at | datetime | no | optional |
| remember_token | text | no | optional |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Relationships:

- belongs to one role
- has exactly one profile

### 8.2 Profile

Purpose:

- domain identity record for a user

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| type | enum | yes | admin, teacher, student, parent |
| first_name | text | yes | |
| last_name | text | yes | |
| dob | date-like text | yes | treated as date |
| sex | enum | yes | male, female |
| phone_number | text | no | |
| is_active | boolean | yes | default true |
| id_number | text | yes | unique |
| user_id | identifier | yes | unique logical foreign key to User |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Derived display value:

- full name = first_name + space + last_name

Relationships:

- student profile has many course enrollments
- teacher profile has many class-teacher assignments
- parent profile has many parent-student relationships
- student profile has many activity logs

### 8.3 Role

Purpose:

- defines authorization group

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | should be unique in practice |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Canonical values:

- admin
- student
- teacher
- parent

### 8.4 Grade

Purpose:

- academic level or cohort descriptor

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

### 8.5 Subject

Purpose:

- curriculum subject for a class

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

### 8.6 Term

Purpose:

- academic period used to validate activity creation

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| start | date-like text | yes | treated as date |
| end | date-like text | yes | treated as date |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

### 8.7 Course

Purpose:

- class definition

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| code | text | no | unique when present |
| description | text | no | |
| grade_id | identifier | yes | logical foreign key to Grade |
| subject_id | identifier | yes | logical foreign key to Subject |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Relationships:

- belongs to one grade
- belongs to one subject
- has one active teacher assignment at a time
- has many active student enrollments
- has many activities

### 8.8 CourseTeacher

Purpose:

- teacher assignment history for a class

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| course_id | identifier | yes | logical foreign key to Course |
| teacher_id | identifier | yes | logical foreign key to Profile of type teacher |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Key rule:

- only one active teacher assignment may exist for a course at a time

### 8.9 CourseStudent

Purpose:

- student enrollment record for a class

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| course_id | identifier | yes | logical foreign key to Course |
| student_id | identifier | yes | logical foreign key to Profile of type student |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Recommended uniqueness rule for faithful recreation:

- unique pair on course_id + student_id

### 8.10 ActivityType

Purpose:

- controls how an activity behaves and is presented

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| description | text | no | up to 1000 characters |
| type | enum | yes | boolean, value, static |
| is_active | boolean | yes | default true |
| image | text | yes | image path or asset reference |
| true_value | text | no | boolean label |
| false_value | text | no | boolean label |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

### 8.11 Activity

Purpose:

- class-level academic item such as homework, attendance, or study material

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| teacher_id | identifier | yes | logical foreign key to Profile of type teacher |
| activity_type_id | identifier | yes | logical foreign key to ActivityType |
| course_id | identifier | yes | logical foreign key to Course |
| term_id | identifier | yes | logical foreign key to Term |
| note | text | no | up to 4000 characters |
| total | number | no | full marks for value-based activities |
| due_date | date-like text | no | |
| file | text | no | teacher-provided attachment |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

### 8.12 ActivityLog

Purpose:

- per-student tracking record for an activity

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| student_id | identifier | yes | logical foreign key to Profile of type student |
| activity_id | identifier | yes | logical foreign key to Activity |
| score | number | no | interpretation depends on activity type |
| status | text | yes | default incomplete |
| file | text | no | reserved for student-specific attachment/submission |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Interpretation rules:

- value activity: score is the student's mark
- boolean activity: score `1` means false, score `2` means true
- static activity: score is usually empty

Recommended uniqueness rule for faithful recreation:

- unique pair on activity_id + student_id

### 8.13 Relationship

Purpose:

- link parent profiles to student profiles

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| student_id | identifier | yes | logical foreign key to Profile of type student |
| parent_id | identifier | yes | logical foreign key to Profile of type parent |
| is_active | boolean | yes | default true |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Recommended uniqueness rule for faithful recreation:

- unique pair on student_id + parent_id

### 8.14 School

Purpose:

- reserved institution-level record

Fields:

| Field | Type | Required | Rules |
| --- | --- | --- | --- |
| id | identifier | yes | primary key |
| name | text | yes | |
| logo | text | no | |
| description | text | no | |
| admin | text | no | descriptive field |
| created_at | datetime | yes | |
| updated_at | datetime | yes | |

Current-state note:

- this entity exists in the data model but has no active web workflow in the current product

## 9. Cross-Entity Rules and Invariants

The following behaviors are essential to faithful recreation.

1. Every user must have exactly one role.
2. Every user must have exactly one profile.
3. Profile type must agree with the selected role.
4. User email must be unique.
5. Profile identification number must be unique.
6. A profile may be deactivated without deleting the underlying records.
7. Most operational deletion behavior is soft-deactivation through an `is_active` flag.
8. A class may have many students but only one active teacher assignment at a time.
9. Reassigning a class to a new teacher must deactivate the old assignment and create a new active one.
10. Enrolling a student into a class must reactivate an inactive historical enrollment instead of duplicating it.
11. Linking a parent to a student must reactivate an inactive historical relationship instead of duplicating it.
12. Creating an activity must fail if no active term covers the current date.
13. Creating an activity must initialize activity-log records for all currently active students in that class.
14. Value activity scores must never exceed the activity total.
15. Boolean activity logging must explicitly mark unchecked students as false.
16. Student-facing pages must never expose a class unless the student is actively enrolled in it.
17. Parent is a valid user type but does not have a current end-user web area.

## 10. Search, Lists, and Administrative UX

The system should support fast operational maintenance through searchable tabular lists.

At minimum:

- classes list is searchable and paginated
- students list is searchable and paginated
- users list is searchable and paginated
- result count or visible total is shown near the list
- create/edit actions are available inline from list screens where appropriate

## 11. Notifications and Errors

The system uses simple success and error messages after actions.

It should surface clear feedback for at least these cases:

- class created
- class updated
- class status updated
- user created
- user updated
- user status updated
- term/grade/subject/activity type created or updated
- relationship added or removed
- student enrolled or un-enrolled
- activity created
- activity logged
- duplicate relationship rejected
- attempt to create activity without an active current term rejected
- invalid old password rejected
- mismatched password confirmation rejected
- mark above total rejected
- unauthorized route access rejected with a forbidden response

## 12. Seed and Bootstrap Data

A fresh system should contain the following default records.

### 12.1 Roles

- admin
- student
- teacher
- parent

### 12.2 Default Admin User

Create one default administrator:

| Field | Value |
| --- | --- |
| name | Admin Person |
| email | admin@example.com |
| password | 12345 |
| role | admin |
| profile type | admin |
| first name | Admin |
| last name | Person |
| dob | 1990-01-01 |
| sex | male |
| id number | 00000 |
| phone number | 0000000000 |

### 12.3 Default Grades

- Grade 1
- Grade 2
- Grade 3
- Form 1
- Form 2
- Form 3

### 12.4 Default Subjects

- Art
- English
- Mathematics
- Shona

### 12.5 Default Term

Create one initial term:

| Field | Value |
| --- | --- |
| name | 2024 Term 1 |
| start | 2024-01-01 |
| end | 2024-04-30 |

### 12.6 Default Activity Types

Create three initial activity types.

#### Homework

| Field | Value |
| --- | --- |
| name | Homework |
| description | You will have no free time under our care. You are welcome |
| type | value |
| image | any valid asset reference |

#### Attendance

| Field | Value |
| --- | --- |
| name | Attendance |
| description | Why were you absent?What do you mean the dog swallowed your bus fare? |
| type | boolean |
| image | any valid asset reference |
| true label | Present |
| false label | Absent |

#### Study Material

| Field | Value |
| --- | --- |
| name | Study Material |
| description | I know you are not gonna read these anyway but as your teacher its my job to give these to you. |
| type | static |
| image | any valid asset reference |

## 13. Reporting Logic

The current dashboard reports are simple and should be reproduced faithfully.

### 13.1 Student Count

- count active student profiles

### 13.2 Teacher Count

- count active teacher profiles

### 13.3 Class Distribution

- for each active class, count active student enrollments
- present the distribution visually and as a ranked list

### 13.4 Absence Trend

- use attendance activities only
- treat false attendance result as an absence
- current-window count: absences created within the last five days
- prior-window count: absences created between five and twelve days ago
- show the difference between the two windows

## 14. File and Attachment Semantics

The system contains two different file concepts.

### 14.1 Activity File

- attached to the activity itself
- supplied by staff when creating the activity
- used for shared assignment or study material documents

### 14.2 Activity Log File

- attached to the student-specific activity log
- intended for student-specific submissions or artifacts
- visible on student/staff result pages if populated
- not actively populated by the current web workflows

## 15. Reconstruction Notes

To recreate the product faithfully, preserve the following characteristics even if you choose a different technical architecture:

- role-based navigation separation between staff and students
- user/profile split where profile carries the domain identity
- class roster history through soft-active enrollment records
- teacher assignment history through soft-active class-teacher records
- activity-type-driven behavior for logging and display
- automatic activity-log initialization for currently enrolled students
- current-term validation before activity creation
- simple flash-message feedback after all mutations
- search-oriented list pages for staff administration
- parent records as managed relationships rather than a completed portal

## 16. Out-of-Scope or Incomplete Areas in the Current Web Product

These items exist in the current system state but are either incomplete, latent, or non-primary.

- API behavior exists separately but is intentionally excluded from this document.
- School exists as a data entity but has no active web workflow.
- Parent exists as a role and profile type but has no dedicated parent-facing web area.
- Student submission upload through activity-log files is not implemented in the current web scope.
- Class activity editing is surfaced in the interface but not functionally completed.
- A secondary admin-only home page exists outside the main dashboard flow, but it is not central to the product behavior.

## 17. Acceptance Criteria for a Faithful Rebuild

The recreated system should be considered faithful if all of the following are true:

1. An admin can sign in with the default seeded account and manage users, settings, classes, students, parents, and teachers.
2. A teacher can sign in, open the staff dashboard, manage classes, create activities, and log marks or attendance.
3. A student can sign in, see only their own classes, and view only their own per-class results.
4. A class can be created with a grade, subject, optional description, and optional teacher assignment.
5. Students can be enrolled, un-enrolled, copied to other classes, and moved between classes without creating duplicate active enrollments.
6. Parents can be linked to students and later unlinked through deactivation.
7. Creating an activity automatically creates one tracking record per currently enrolled student.
8. Numeric activities reject marks above the configured total.
9. Attendance activities store and display Present/Absent using a boolean-style result model.
10. Staff dashboard analytics show student count, teacher count, class distribution, and recent absence trends.
11. The system behaves correctly even after records are deactivated and later reactivated.
