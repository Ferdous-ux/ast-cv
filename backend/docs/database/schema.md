# AST-CV Database Schema

## 1. Overview

AST-CV uses a relational MySQL database designed to support:

* User accounts
* User profiles
* Resume management
* Resume versioning
* Resume content
* ATS analysis
* AI-powered resume optimization
* Future job matching capabilities

The database follows a relational structure with foreign keys, indexes, unique constraints, and cascading rules.

---

## 2. Core Entities

### Users

Stores authentication and account-level information.

**Table:** `users`

Main responsibilities:

* Account identity
* Authentication
* Account status
* Login information

---

### Profiles

Stores the user's persistent personal profile.

**Table:** `profiles`

Relationship:

```text
users 1 ─── 1 profiles
```

Main fields:

* `first_name`
* `last_name`
* `headline`
* `phone`
* `country`
* `city`
* `avatar_path`
* `website_url`
* `linkedin_url`
* `github_url`
* `portfolio_url`

A user can have only one profile.

---

### Resumes

Represents a user's resume.

**Table:** `resumes`

Relationship:

```text
users 1 ─── N resumes
```

Main fields:

* `user_id`
* `title`
* `status`
* `current_version_id`

A user may create multiple resumes for different purposes or job applications.

---

### Resume Versions

Represents a version of a resume.

**Table:** `resume_versions`

Relationship:

```text
resumes 1 ─── N resume_versions
```

Main fields:

* `resume_id`
* `version_number`
* `status`
* `summary`
* `template`

Each resume can have multiple versions.

The combination of:

```text
resume_id + version_number
```

must be unique.

---

## 3. Resume Content

Resume content is currently associated with a specific `resume_version`.

### Experiences

**Table:** `resume_experiences`

Stores professional work experience.

Relationship:

```text
resume_versions 1 ─── N resume_experiences
```

Main fields:

* `company_name`
* `job_title`
* `location`
* `start_date`
* `end_date`
* `is_current`
* `description`
* `sort_order`

---

### Education

**Table:** `resume_educations`

Stores educational history.

Main fields:

* `institution`
* `degree`
* `field_of_study`
* `location`
* `start_date`
* `end_date`
* `description`
* `sort_order`

Relationship:

```text
resume_versions 1 ─── N resume_educations
```

---

### Skills

**Table:** `skills`

Global/master skill records.

Examples:

```text
Flutter
Laravel
PHP
MySQL
Git
```

The skill name is unique.

---

### Resume Skills

**Table:** `resume_skills`

Connects resume versions with skills.

Relationship:

```text
resume_versions N ─── N skills
```

The relationship is implemented through:

```text
resume_skills
```

Additional information can be stored for a specific resume:

* `level`
* `sort_order`

The combination of:

```text
resume_version_id + skill_id
```

must be unique.

---

### Languages

**Table:** `languages`

Global/master language records.

Examples:

```text
Arabic
English
French
```

Language names are unique.

---

### Resume Languages

**Table:** `resume_languages`

Connects resume versions with languages.

Relationship:

```text
resume_versions N ─── N languages
```

Additional information:

* `proficiency`
* `sort_order`

The combination of:

```text
resume_version_id + language_id
```

must be unique.

---

### Projects

**Table:** `resume_projects`

Stores projects included in a resume version.

Main fields:

* `name`
* `description`
* `project_url`
* `repository_url`
* `start_date`
* `end_date`
* `sort_order`

---

### Certificates

**Table:** `resume_certificates`

Stores certificates included in a resume version.

Main fields:

* `name`
* `issuer`
* `credential_id`
* `credential_url`
* `issued_at`
* `expires_at`
* `sort_order`

---

### Awards

**Table:** `resume_awards`

Stores awards and achievements.

Main fields:

* `title`
* `issuer`
* `description`
* `awarded_at`
* `url`
* `sort_order`

---

### Publications

**Table:** `resume_publications`

Stores publications.

Main fields:

* `title`
* `publisher`
* `description`
* `published_at`
* `url`
* `sort_order`

---

### Resume Links

**Table:** `resume_links`

Stores additional links displayed on a specific resume version.

Main fields:

* `label`
* `url`
* `sort_order`

---

## 4. Referential Integrity

Foreign key relationships are used to maintain data integrity.

Resume-owned records generally use:

```text
ON DELETE CASCADE
```

This means deleting a parent resume/version removes its dependent records.

Master data referenced by resumes, such as:

```text
skills
languages
```

uses:

```text
ON DELETE RESTRICT
```

This prevents deleting master records that are still referenced.

---

## 5. Ordering

Resume content that can appear in different positions uses:

```text
sort_order
```

This allows the application to control presentation order without relying on database insertion order.

---

## 6. Versioning Strategy

Resume versions provide historical snapshots of a resume.

Conceptually:

```text
Resume
│
├── Version 1
├── Version 2
├── Version 3
└── Current Version
```

The `resumes.current_version_id` field identifies the currently selected version.

This allows the system to support future features such as:

* Resume history
* Resume duplication
* Job-specific resumes
* Version comparison
* Restore previous versions

---

## 7. Future ATS Layer

ATS functionality will be designed as a separate domain layer rather than adding ATS-specific fields directly to the core resume tables.

Conceptually:

```text
Resume
   │
   └── ATS Analysis
          │
          ├── Score
          ├── Keywords
          ├── Missing Keywords
          ├── Suggestions
          └── Job Match
```

The exact ATS schema will be defined separately after the core resume domain is finalized.

---

## 8. Future AI Layer

AI functionality will operate on top of the resume domain.

Potential capabilities include:

* Resume optimization
* Experience rewriting
* Keyword optimization
* Job matching
* Content suggestions
* Resume quality analysis

AI-specific data should remain separated from the core resume tables where practical.

---

## 9. Architectural Principle

The database is designed around a modular-monolith architecture.

The current core domains are:

```text
Identity
Profile
Resume
Resume Content
Master Data
```

Future domains may include:

```text
ATS
AI
Jobs
Applications
Analytics
Subscriptions
```

New functionality should be added through clearly separated modules rather than creating an unstructured collection of tables.
