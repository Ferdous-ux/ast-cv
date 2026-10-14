# AST-CV Database Relationships

## 1. User → Profile

```text
users 1 ─── 1 profiles
```

A user has exactly one profile.

### Foreign Key

```text
profiles.user_id → users.id
```

### Delete Behavior

```text
ON DELETE CASCADE
```

Deleting a user deletes their profile.

---

## 2. User → Resumes

```text
users 1 ─── N resumes
```

A user can create multiple resumes.

### Foreign Key

```text
resumes.user_id → users.id
```

### Delete Behavior

```text
ON DELETE CASCADE
```

Deleting a user deletes their resumes.

---

## 3. Resume → Resume Versions

```text
resumes 1 ─── N resume_versions
```

A resume can have multiple versions.

### Foreign Key

```text
resume_versions.resume_id → resumes.id
```

### Delete Behavior

```text
ON DELETE CASCADE
```

Deleting a resume deletes all of its versions.

---

## 4. Resume → Current Version

```text
resumes.current_version_id → resume_versions.id
```

The `current_version_id` field identifies the active version of the resume.

This relationship is intentionally nullable because a newly created resume may not have a version yet.

---

## 5. Resume Version → Experiences

```text
resume_versions 1 ─── N resume_experiences
```

A resume version can contain multiple work experiences.

### Foreign Key

```text
resume_experiences.resume_version_id
    → resume_versions.id
```

### Delete Behavior

```text
ON DELETE CASCADE
```

Deleting a resume version deletes its experiences.

---

## 6. Resume Version → Education

```text
resume_versions 1 ─── N resume_educations
```

A resume version can contain multiple education records.

### Foreign Key

```text
resume_educations.resume_version_id
    → resume_versions.id
```

### Delete Behavior

```text
ON DELETE CASCADE
```

---

## 7. Resume Version ↔ Skills

```text
resume_versions N ─── N skills
```

This many-to-many relationship is implemented using:

```text
resume_skills
```

Structure:

```text
resume_versions
       │
       │
       ▼
resume_skills
       │
       │
       ▼
skills
```

### Foreign Keys

```text
resume_skills.resume_version_id
    → resume_versions.id

resume_skills.skill_id
    → skills.id
```

The relationship also stores:

```text
level
sort_order
```

### Delete Behavior

Resume version:

```text
CASCADE
```

Skill:

```text
RESTRICT
```

---

## 8. Resume Version ↔ Languages

```text
resume_versions N ─── N languages
```

Implemented using:

```text
resume_languages
```

Structure:

```text
resume_versions
       │
       ▼
resume_languages
       │
       ▼
languages
```

### Foreign Keys

```text
resume_languages.resume_version_id
    → resume_versions.id

resume_languages.language_id
    → languages.id
```

Additional relationship data:

```text
proficiency
sort_order
```

### Delete Behavior

Resume version:

```text
CASCADE
```

Language:

```text
RESTRICT
```

---

## 9. Resume Version → Projects

```text
resume_versions 1 ─── N resume_projects
```

Foreign key:

```text
resume_projects.resume_version_id
    → resume_versions.id
```

Delete behavior:

```text
CASCADE
```

---

## 10. Resume Version → Certificates

```text
resume_versions 1 ─── N resume_certificates
```

Foreign key:

```text
resume_certificates.resume_version_id
    → resume_versions.id
```

Delete behavior:

```text
CASCADE
```

---

## 11. Resume Version → Awards

```text
resume_versions 1 ─── N resume_awards
```

Foreign key:

```text
resume_awards.resume_version_id
    → resume_versions.id
```

Delete behavior:

```text
CASCADE
```

---

## 12. Resume Version → Publications

```text
resume_versions 1 ─── N resume_publications
```

Foreign key:

```text
resume_publications.resume_version_id
    → resume_versions.id
```

Delete behavior:

```text
CASCADE
```

---

## 13. Resume Version → Links

```text
resume_versions 1 ─── N resume_links
```

Foreign key:

```text
resume_links.resume_version_id
    → resume_versions.id
```

Delete behavior:

```text
CASCADE
```

---

# 14. Complete Relationship Map

```text
                           ┌──────────────┐
                           │    users     │
                           └──────┬───────┘
                                  │
                     ┌────────────┴────────────┐
                     │                         │
                     ▼                         ▼
              ┌─────────────┐          ┌─────────────┐
              │   profiles  │          │   resumes   │
              └─────────────┘          └──────┬──────┘
                                              │
                                              ▼
                                     ┌──────────────────┐
                                     │ resume_versions  │
                                     └────────┬─────────┘
                                              │
              ┌───────────────┬───────────────┼───────────────┐
              │               │               │               │
              ▼               ▼               ▼               ▼
       experiences       educations       projects       certificates
              │
              │
              ├────────────── awards
              │
              ├────────────── publications
              │
              └────────────── links

                     resume_versions
                           │
                  ┌────────┴────────┐
                  ▼                 ▼
            resume_skills    resume_languages
                  │                 │
                  ▼                 ▼
                skills          languages
```

---

# 15. Relationship Rules

The following rules must be preserved throughout the application:

### User ownership

Every resume belongs to a user.

```text
resume.user_id
```

must reference an existing user.

---

### Resume version ownership

Every version belongs to exactly one resume.

```text
resume_version.resume_id
```

must reference an existing resume.

---

### Version content ownership

Experiences, education, projects, certificates, awards, publications, and links belong to a specific resume version.

---

### Master data

`skills` and `languages` are shared master data.

They are not duplicated for every resume.

---

### Ordering

Content collections use:

```text
sort_order
```

to control their order in the generated resume.

---

# 16. Application-Level Rule

The database guarantees relational integrity, but business rules must also be enforced by the Laravel application.

Examples:

```text
A user can only access their own resumes.

A user cannot edit another user's resume.

current_version_id must belong to the same resume.

A resume version cannot be accessed by a user who does not own its parent resume.
```

These rules will be implemented later through:

```text
Policies
Services
Form Requests
Authorization
```

---

# 17. Future Extensions

Future modules can attach to the resume domain without changing the existing core relationships.

Potential modules:

```text
ATS
AI
Jobs
Applications
Analytics
Subscriptions
```

The core resume domain should remain stable while these modules evolve independently.
