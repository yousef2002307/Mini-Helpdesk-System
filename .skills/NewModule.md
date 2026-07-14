# Agent Skill: Generate New Module Architecture

## Context & Objective
When the user asks to create a new "module", "feature", or "API endpoint layout", you must scaffold the files across our multi-layered system architecture. We strictly decouple business logic using a Layered Architecture: **Controller ➔  FormRequest ➔ Resource➔ Service ➔ Repository (if needed)**. 

Every new module must be explicitly assigned to one of three context folders/namespaces:
1. `Admin` (Backoffice, management, and superadmin actions)
2. `User` (Client-facing, consumer app, or driver app flows)
3. `Shared` (Cross-cutting features, public lookups, or internal shared utilities)

---

## Directory & Namespace Map

You must organize your generated code blocks according to this strict file layout:

```text
app/
├── Http/
│   └── Controllers/
│       ├── Admin/        # Admin-specific API logic
│       ├── User/         # Client/Driver-specific API logic
│       └── Shared/       # Public or cross-context controllers
├── Http/
│   └── Resources/
│       ├── Admin/        # Custom response structures for admin
│       ├── User/         # Custom response structures for users
│       └── Shared/       # Generic API transformers
├── Http/
│   └── Requests/
│       ├── Admin/        # Custom Validation structures for admin
│       ├── User/         # Custom Validation structures for users
│       └── Shared/       # Generic API transformers
├── Services/
│   ├── Admin/            # Admin business rules & heavy lifting
│   ├── User/             # User specific flows
│   └── Shared/           # Common operational logic
└── Repositories/
    ├── Admin/            # Complex custom admin queries (if needed)
    ├── User/             # Complex user queries (if needed)
    └── Shared/           # Base or shared database operations