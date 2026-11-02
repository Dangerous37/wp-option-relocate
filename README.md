# WordPress Option Relocate
Preview and reverse an origin change inside a selected WordPress option, including nested arrays stored with PHP serialization. A host such as old.example.test.evil is outside the requested origin.

With PHP 8.4 and WP-CLI, run from the checkout:

```sh
wp --path=/path/to/wp eval-file relocate.php plan theme_links https://old.example.test https://new.example.test /private/plan.json
wp --path=/path/to/wp eval-file relocate.php apply /private/plan.json
wp --path=/path/to/wp eval-file relocate.php undo /private/plan.json
```

Use a fresh path for each plan. Only migrate options you understand, with a backup and other writers paused. For a demo on a disposable installation set RELOCATE_LIB to the absolute path of library.php and run `wp --path=/path/to/wp eval-file /absolute/path/to/acceptance.php`. Tests exercise the real option API and inspect stored serialization.

## Resumen en español
Previsualiza, aplica y revierte una migración de enlaces en una opción WordPress, incluidas matrices anidadas. Detecta modificaciones posteriores antes de revertir.
