# AST-CV Database ERD

## Core Database Structure

```text
┌──────────────────────┐
│        users         │
├──────────────────────┤
│ id PK                │
│ name                 │
│ email UNIQUE         │
│ password             │
│ status               │
│ last_login_at        │
│ created_at           │
│ updated_at           │
└──────────┬───────────┘
           │
           │ 1 : 1
           ▼
┌──────────────────────┐
│       profiles       │
├──────────────────────┤
│ id PK                │
│ user_id FK UNIQUE    │
│ first_name           │
│ last_name            │
│ headline             │
│ phone                │
│ country              │
│ city                 │
│ avatar_path          │
│ website_url          │
│ linkedin_url         │
│ github_url           │
│ portfolio_url        │
└──────────────────────┘


users
  │
  │ 1 : N
  ▼
┌────────────────────────┐
│        resumes         │
├────────────────────────┤
│ id PK                  │
│ user_id FK             │
│ title                  │
│ status                 │
│ current_version_id FK  │
│ created_at             │
│ updated_at             │
└───────────┬────────────┘
            │
            │ 1 : N
            ▼
┌────────────────────────────┐
│      resume_versions       │
├────────────────────────────┤
│ id PK                      │
│ resume_id FK               │
│ version_number             │
│ status                     │
│ summary                    │
│ template                   │
│ created_at                 │
│ updated_at                 │
└────────────┬───────────────┘
             │
             ├────────────── 1 : N ──► resume_experiences
             │
             ├────────────── 1 : N ──► resume_educations
             │
             ├────────────── 1 : N ──► resume_projects
             │
             ├────────────── 1 : N ──► resume_certificates
             │
             ├────────────── 1 : N ──► resume_awards
             │
             ├────────────── 1 : N ──► resume_publications
             │
             └────────────── 1 : N ──► resume_links
```

## Skills Relationship

```text
┌──────────────────────┐
│   resume_versions    │
└──────────┬───────────┘
           │
           │ 1 : N
           ▼
┌──────────────────────┐
│    resume_skills     │
├──────────────────────┤
│ id PK                │
│ resume_version_id FK │
│ skill_id FK          │
│ level                │
│ sort_order           │
└──────────┬───────────┘
           │
           │ N : 1
           ▼
┌──────────────────────┐
│        skills        │
├──────────────────────┤
│ id PK                │
│ name UNIQUE          │
│ category             │
└──────────────────────┘
```

Therefore:

```text
resume_versions N : N skills
```

through:

```text
resume_skills
```

---

## Languages Relationship

```text
┌──────────────────────┐
│   resume_versions    │
└──────────┬───────────┘
           │
           │ 1 : N
           ▼
┌──────────────────────┐
│  resume_languages    │
├──────────────────────┤
│ id PK                │
│ resume_version_id FK │
│ language_id FK       │
│ proficiency          │
│ sort_order            │
└──────────┬───────────┘
           │
           │ N : 1
           ▼
┌──────────────────────┐
│      languages       │
├──────────────────────┤
│ id PK                │
│ name UNIQUE          │
└──────────────────────┘
```

Therefore:

```text
resume_versions N : N languages
```

through:

```text
resume_languages
```

---

## Complete Relationship Overview

```text
users
 │
 ├── 1 : 1 ── profiles
 │
 └── 1 : N ── resumes
                │
                └── 1 : N ── resume_versions
                                │
                                ├── 1 : N ── resume_experiences
                                │
                                ├── 1 : N ── resume_educations
                                │
                                ├── 1 : N ── resume_projects
                                │
                                ├── 1 : N ── resume_certificates
                                │
                                ├── 1 : N ── resume_awards
                                │
                                ├── 1 : N ── resume_publications
                                │
                                ├── 1 : N ── resume_links
                                │
                                ├── N : N ── skills
                                │              │
                                │              └── resume_skills
                                │
                                └── N : N ── languages
                                               │
                                               └── resume_languages
```

## Design Principles

1. Users own their resumes.
2. Resumes own their versions.
3. Resume content belongs to a specific version.
4. Skills and languages are shared master data.
5. Many-to-many relationships use dedicated pivot tables.
6. Ordered content uses `sort_order`.
7. Dependent resume data uses cascade deletion.
8. Shared master data uses restricted deletion.
9. `current_version_id` identifies the active resume version.
10. Business authorization rules will be enforced at the Laravel application layer.

```
```
