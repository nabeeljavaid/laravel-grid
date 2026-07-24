# Laravel Grid

A lightweight Laravel package for rendering data grids with listing, searching, sorting, and pagination — no JavaScript framework required. Ships with Bootstrap and Tailwind view themes and a small jQuery plugin for interactivity (sorting links, pagination, bulk actions, AJAX cell edits).

- **Package:** `nabeeljavaid/laravel-grid`
- **Namespace:** `NabeelJavaid\LaravelGrid`

## Requirements

- PHP `^8.2`
- Laravel (`illuminate/support`, `illuminate/view`) `^8.0` through `^13.0`
- The Laravel `Form` facade (e.g. [`laravelcollective/html`](https://github.com/LaravelCollective/html) or equivalent) — used by the search form partials to render `text`/`select`/`radios`/`checkboxes` fields
- Front-end, loaded by the host application (not bundled, except jQuery Query which ships in `resources/js`):
  - jQuery
  - [SweetAlert2](https://sweetalert2.github.io/) (`Swal`) — confirmation dialogs
  - [Toastr](https://github.com/CodeSeven/toastr) — success/error notifications
  - [jQuery BlockUI](https://github.com/malsup/blockui) — busy overlay during AJAX calls
  - Font Awesome — action/sort icons

## Installation

```bash
composer require nabeeljavaid/laravel-grid
```

The service provider (`LaravelGridServiceProvider`) is auto-discovered via Laravel package discovery. This project also registers a class alias in `config/app.php`:

```php
'aliases' => [
    // ...
    'Grid' => NabeelJavaid\LaravelGrid\LaravelGrid::class,
],
```

### Publishing assets

```bash
# Config file -> config/laravel-grid.php
php artisan vendor:publish --tag=laravel-grid-config

# CSS/JS -> public/vendor/laravel-grid
php artisan vendor:publish --tag=laravel-grid-assets

# Blade views -> resources/views/vendor/laravel-grid
php artisan vendor:publish --tag=laravel-grid-views
```

Include the published assets in your layout:

```html
<link rel="stylesheet" href="{{ asset('vendor/laravel-grid/css/laravel-grid.css') }}">
<script src="{{ asset('vendor/laravel-grid/js/jquery.query-2.1.7.js') }}"></script>
<script src="{{ asset('vendor/laravel-grid/js/laravel-grid.js') }}"></script>
```

## Configuration

`config/laravel-grid.php`:

| Key | Options | Default | Description |
| --- | --- | --- | --- |
| `css_framework` | `bootstrap`, `tailwind` | `bootstrap` | Which view/button theme the grid renders. |
| `pagination_style` | `INPUT`, `SELECT`, `LINKS` | `INPUT` | How the page navigator is rendered — free-text page field, a page `<select>`, or Laravel's built-in paginator links. |
| `records_per_page_style` | `SELECT`, `INPUT`, `LINKS` | `SELECT` | How the records-per-page control is rendered. |
| `records_per_page` | array of ints | `[25, 50, 100, 200, 500]` | Options shown in the records-per-page control. |
| `default_limit` | int | `25` | Records per page when none is present in the request/session. |
| `serial_number` | bool | `true` | Show the `#` row-number column (ignored when bulk actions/checkboxes are enabled). |

Each option can also be set via env vars: `GRID_CSS_FRAMEWORK`, `GRID_PAGINATION_STYLE`, `GRID_RECORDS_PER_PAGE_STYLE`, `GRID_DEFAULT_LIMIT`.

## Usage

### Controller

```php
use NabeelJavaid\LaravelGrid\LaravelGrid;
use App\Models\User;

public function index(LaravelGrid $grid)
{
    $grid->setGridName('users-grid')
        ->setBaseUrl(route('users.index'))
        ->setPrimaryKey('id')
        ->setSerialNumber(true)
        ->setColumns([
            [
                'name'       => 'name',
                'label'      => 'Name',
                'width'      => '25%',
                'sortable'   => true,
                'searchable' => true,
                'value'      => fn($row) => e($row->name),
            ],
            [
                'name'       => 'email',
                'label'      => 'Email',
                'width'      => '25%',
                'sortable'   => true,
                'searchable' => true,
                'searchfield' => ['type' => 'text'],
                'value'      => fn($row) => e($row->email),
            ],
            [
                'name'       => 'status',
                'label'      => 'Status',
                'width'      => '15%',
                'sortable'   => false,
                'searchable' => true,
                'searchfield' => [
                    'type'    => 'select',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                ],
                'value' => fn($row) => $row->status === 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>',
            ],
            [
                'name'       => 'actions',
                'label'      => 'Actions',
                'width'      => '10%',
                'sortable'   => false,
                'searchable' => false,
                'value'      => fn($row) => '<a href="' . route('users.edit', $row->id) . '" class="btn btn-sm btn-success"><i class="fa fa-edit"></i></a>',
            ],
        ])
        ->setMainActions([
            ['name' => 'refresh', 'title' => 'Refresh', 'url' => route('users.index')],
            ['name' => 'add',     'title' => 'Add User', 'url' => route('users.create')],
            ['name' => 'export',  'title' => 'Export',   'url' => route('users.export')],
        ])
        ->setBulkActions([
            route('users.bulk-delete') => 'Delete Selected',
        ])
        ->setPaginator(User::query(), 'name', 'asc');

    return view('users.index', compact('grid'));
}
```

### View

```blade
{{ $grid->render() }}

@push('scripts')
<script>
    $(function () {
        $('#users-grid').laravelGrid();
    });
</script>
@endpush
```

`render()` returns the `laravel-grid::grid` view, which picks the Bootstrap or Tailwind partials based on `laravel-grid.css_framework` and includes the header (actions + search + bulk actions), body (table), and footer (pagination + records-per-page + display info).

## API reference

### `LaravelGrid`

| Method | Description |
| --- | --- |
| `setGridName(string $name)` | Sets the wrapping `<div id="...">` used by the JS plugin selector. Default: `grid`. |
| `setBaseUrl(string $url)` | Base URL used to build sort/pagination links and to key the session state per grid instance. |
| `setPrimaryKey(string $key)` | Row identifier used for bulk-action checkboxes. Default: `id`. |
| `setSerialNumber(bool $value)` | Toggle the `#` column. Overrides the `laravel-grid.serial_number` config value. |
| `setColumns(array $cols)` | Defines the table columns (see **Column definition** below). |
| `setMainActions(array $actions)` | Toolbar buttons rendered above the search form (see **Main actions** below). |
| `setBulkActions(array $actions)` | `['url' => 'label']` map rendered as buttons that POST the checked row IDs to `url`. |
| `setPaginator($query, $orderBy, $order, $limit = null)` | Applies sort/search/limit from the request or session to `$query`, paginates it, and stores pagination metadata. Must be called last, after columns/actions are set. |
| `getData()` | Raw grid state array (`order_by`, `order`, `limit`, `page`, `total`, `search`, etc.) — useful for building custom views. |
| `getRows()` | The underlying `LengthAwarePaginator` instance. |
| `buildUrl(array $params)` | Builds a URL preserving current grid state, overriding just the given params (used for sort/page/limit links). |
| `render()` | Returns the `laravel-grid::grid` Blade view. |

### Column definition

Each entry in `setColumns()` accepts:

| Key | Type | Description |
| --- | --- | --- |
| `name` | string | Field/column identifier (used for `order_by` and search keys). |
| `label` | string | Column header text. |
| `width` | string | `<th width="...">` value. |
| `sortable` | bool | Renders the header as a sort link. |
| `searchable` | bool | Includes the column in the search panel. |
| `searchfield` | array | Optional. `type` (`text`, `select`, `radios`, `checkboxes`), `name` (defaults to `name`), `options` (for `select`/`radios`/`checkboxes`), `attr` (extra HTML attributes), `help` (popover help text). |
| `value` | callable | `fn($row) => string`. Receives the row model and returns the raw (unescaped) cell HTML. Escape any user-provided data yourself. Columns without a `value` callback render `N/A`. |

### Main actions

`setMainActions()` takes an array of `['name' => ..., 'title' => ..., 'url' => ..., 'target' => ..., 'method' => ..., 'id' => ..., 'class' => ..., 'icon' => ...]`. Only `name` and `url` are required — the rest fall back to sensible defaults. `name` must be one of the built-in button styles, each with its own client-side behavior in `laravel-grid.js`:

| `name` | Behavior |
| --- | --- |
| `refresh` | Navigates to `url`. |
| `add` / `import` / `export` / `upload` / `download` / `button` | Navigates to `url`. `export` is blocked client-side with a SweetAlert error if the grid's total row count exceeds `settings.export.max_limit` (config-driven). |
| `edit` | Requires exactly one checked row; navigates to `url/{id}`. |
| `delete` | Requires one or more checked rows; confirms, then POSTs the checked IDs to `url` via AJAX and reloads on success. |
| `print` | Calls `window.print()`. |

### AJAX cell interactions

Elements inside the table body opt into AJAX behavior via CSS classes, without any extra JS wiring:

- `select.laravel-grid-ajax` / `input.laravel-grid-ajax` (with `data-url`) — POSTs the new value to `data-url` on change/blur.
- `a.laravel-grid-ajax` (with `data-url` or `href`, optional `data-confirm`) — GETs the URL, optionally after a confirmation dialog.
- `a.laravel-grid-delete` (with `href`) — confirms, then submits a `DELETE` form to `href`.

All AJAX responses are expected as JSON: `{"status": "success|error|...", "message": "..."}`.

## Themes

Set `laravel-grid.css_framework` to `bootstrap` (default) or `tailwind`. Both themes share the same `LaravelGrid` API and the same structural CSS classes (`laravel-grid-header`, `laravel-grid-body`, `laravel-grid-footer`, `laravel-grid-pagination`, etc.) — only the partials under `src/resources/views/partials` (Bootstrap) vs `src/resources/views/tailwind` differ in markup/utility classes.

## License

MIT © [Nabeel Javaid](mailto:nabeeljavaid.nmj@gmail.com)
