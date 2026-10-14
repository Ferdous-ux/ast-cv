# AST-CV Architecture

## 1. Architecture Overview

AST-CV is built as a modular application consisting of:

- Flutter mobile application
- Laravel REST API
- MySQL database
- AI integration layer

The system is designed to be scalable, maintainable, secure, and easy to extend.

```text
                    ┌─────────────────────┐
                    │    Flutter Mobile    │
                    │       Client        │
                    └──────────┬──────────┘
                               │
                               │ REST API
                               ▼
                    ┌─────────────────────┐
                    │    Laravel API      │
                    │  Modular Monolith   │
                    └──────────┬──────────┘
                               │
              ┌────────────────┼────────────────┐
              │                │                │
              ▼                ▼                ▼
        ┌──────────┐     ┌──────────┐     ┌──────────┐
        │  MySQL   │     │ AI Layer │     │ Storage  │
        └──────────┘     └──────────┘     └──────────┘