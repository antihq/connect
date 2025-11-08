## IMPORTANT

- All tests must follow BDD style.
- Always use the `artisan make:*` command when creating models, migrations, factories, controllers, and other Laravel files. This ensures proper boilerplate, registration, and maintainability.
- Always refactor code to import classes at the top and use the imported class directly (never use the fully qualified namespace in code bodies).
- Always use constructor property promotion for public/protected properties in classes, including models, notifications, and other Laravel files.
- Always use models unguarded (`protected $guarded = [];`), so all attributes are mass assignable.
- Use the `protected casts()` method when casting model attributes, e.g.:
  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
      return [
          'is_admin' => 'boolean',
      ];
  }
- Never use the `.prevent` modifier for `wire:submit` in Livewire forms. The latest Livewire version does not require it.
- Use Blade short syntax (e.g. `:label`, `:placeholder`) whenever possible for passing PHP expressions and translations to components.
