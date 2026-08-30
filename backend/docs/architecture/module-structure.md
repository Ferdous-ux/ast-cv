# Module Structure

## 1. Purpose

AST-CV follows a Modular Monolith architecture.

The backend is a single Laravel application divided into independent business modules.

Each module owns a specific business domain and should minimize direct dependencies on other modules.

---

## 2. Module Structure

Each module follows this general structure:

```text
Module/
├── Domain/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Contracts/
│   └── Exceptions/
│
├── Application/
│   ├── Actions/
│   ├── DTOs/
│   └── Services/
│
├── Infrastructure/
│   ├── Persistence/
│   └── External/
│
└── Presentation/
    ├── Http/
    │   ├── Controllers/
    │   ├── Requests/
    │   └── Resources/
    │
    └── Providers/