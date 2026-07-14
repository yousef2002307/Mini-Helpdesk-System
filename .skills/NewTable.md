# Agent Skill: Generate Complete Laravel Table Lifecycle

## Context & Objective
When the user asks to create a new table, schema, or database entity, you must provide the complete set of Laravel database lifecycle files. Do not just provide the migration; always generate the migration, the Eloquent model (with relationships), the model factory, and the database seeder to ensure a fully functional codebase.

---

## Generation Rules

1. **Artisan Command First:** Always remind the user of the single command to generate these files together:
   `php artisan make:model ModelName -mfs`
2. **Strict Typing:** Use PHP 8+ return type hints for all Eloquent relationship methods (e.g., `: BelongsTo`, `: HasMany`).
3. **Mass Assignment:** Explicitly define the `$fillable` array in the model.
4. **Relational Integrity:** 
   * Use `$table->foreignId('column_id')->constrained()->onDelete('cascade');` in migrations.
   * Provide both sides of the relationship (the new model and the inverse relation for the existing parent/child model).
5. **Self-Contained Factories:** Use relationship factory chaining (e.g., `'category_id' => Category::factory()`) so the factory can run independently.

---
