<?php
/**
 * GRID (Listing, Searching, Sorting and Pagination)
 *
 * @author      Nabeel Javaid <nabeel.bim@myvteams.com>
 * @copyright   Certified Mail Envelopes, Inc.
 * @version     2.0
 */

namespace NabeelJavaid\LaravelGrid;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

class LaravelGrid
{
    private string $gridId = '';
    private string $gridName = 'grid';
    private string $baseUrl = '/';
    private array $data = [];
    private array $mainActions = [];
    private array $bulkActions = [];
    private string $primaryKey = 'id';
    private array $cols = [];
    private $paginator;
    private bool $serialNumber = true;

    private string $paginationStyle;
    private string $recordsPerPageStyle;
    private array $recordsPerPage;

    public function __construct()
    {
        $this->paginationStyle     = config('laravel-grid.pagination_style', 'INPUT');
        $this->recordsPerPageStyle = config('laravel-grid.records_per_page_style', 'SELECT');
        $this->recordsPerPage      = config('laravel-grid.records_per_page', [25, 50, 100, 200, 500]);
        $this->serialNumber        = config('laravel-grid.serial_number', true);
    }

    public function setGridName(string $name): static
    {
        $this->gridName = $name;
        return $this;
    }

    public function getGridName(): string
    {
        return $this->gridName;
    }

    public function setBaseUrl(string $url): static
    {
        $this->baseUrl = $url;
        return $this;
    }

    public function setPrimaryKey(string $key): static
    {
        $this->primaryKey = $key;
        return $this;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setSerialNumber(bool $value): static
    {
        $this->serialNumber = $value;
        return $this;
    }

    public function getSerialNumber(): bool
    {
        return $this->serialNumber;
    }

    public function setPaginator($model, $order_by, $order, $limit = null): static
    {
        if ($limit === null) {
            $limit = config('laravel-grid.default_limit', 25);
        }

        $this->data = $this->getPost();

        // Sort
        if (isset($this->data['order_by']) and isset($this->data['order'])) {
            $order_by = $this->data['order_by'];
            $order = $this->data['order'];
        } else {
            $this->data['order_by'] = $order_by;
            $this->data['order'] = $order;
        }

        // Limit
        if (isset($this->data['limit'])) {
            $limit = $this->data['limit'];
        } else {
            $this->data['limit'] = $limit;
        }

        // Laravel Fix
        if (isset($this->data['page'])) {
            Request::merge(['page' => $this->data['page']]);
        }
        if (isset($this->data['search'])) {
            Request::merge(['search' => $this->data['search']]);
        }

        $paginator = $model->orderBy($order_by, $order)->paginate($limit);

        $page       = $paginator->currentPage();
        $first_page = 1;
        $last_page  = $paginator->lastPage();
        $next_page  = $page + 1 <= $last_page ? $page + 1 : null;
        $prev_page  = $page - 1 > 0 ? $page - 1 : null;

        $this->data['page']        = $paginator->currentPage();
        $this->data['total_pages'] = $paginator->lastPage();
        $this->data['first_page']  = $first_page;
        $this->data['last_page']   = $last_page;
        $this->data['next_page']   = $next_page;
        $this->data['prev_page']   = $prev_page;
        $this->data['from']        = $paginator->firstItem();
        $this->data['to']          = $paginator->lastItem();
        $this->data['total']       = $paginator->total();
        $this->data['links']       = $paginator->links();

        $this->paginator = $paginator;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('laravel-grid::grid', ['grid' => $this]);
    }

    private function getPost(): array
    {
        $post = [];
        $data = [];

        if (Request::all()) {
            $post = Request::all();
            $this->setSessionData($post);
        } else {
            $post = $this->getSessionData();
        }

        $required = ["order_by", "order", "limit", "page"];
        foreach ($post as $key => $value) {
            if (in_array($key, $required)) {
                if ($value != "undefined") { // jQuery problem
                    $data[$key] = trim($value);
                }
            }
        }
        if (isset($post['search'])) {
            foreach ($post['search'] as $key => $value) {
                if ($value != "") {
                    $data['search'][$key] = $value;
                }
            }
        }

        return $data;
    }

    private function setSessionData(array $data): void
    {
        $this->gridId = base64_encode($this->baseUrl);

        $SessionGrid = [];
        $SessionGrid[$this->gridId]['gridId'] = $this->gridId;
        $SessionGrid[$this->gridId]['POST']   = $data;
        Session::put('SessionGrid', $SessionGrid);
    }

    private function getSessionData(): array
    {
        $this->gridId = base64_encode($this->baseUrl);

        $SessionGrid = Session::get('SessionGrid');

        if (isset($SessionGrid[$this->gridId])) {
            return $SessionGrid[$this->gridId]['POST'];
        }

        return [];
    }

    public function setMainActions(array $mainActions = []): static
    {
        $this->mainActions = $mainActions;
        return $this;
    }

    public function getMainActions(): array
    {
        $fw = config('laravel-grid.css_framework', 'bootstrap');

        if ($fw === 'tailwind') {
            $base    = 'inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded focus:outline-none';
            $outline = "$base text-gray-700 bg-white border border-gray-300 hover:bg-gray-50";
            $buttons = [
                'refresh'  => ['icon' => '<i class="fa fa-refresh fas fa-sync-alt"></i>', 'class' => "$base text-white bg-gray-700 hover:bg-gray-800"],
                'add'      => ['icon' => '<i class="fa fa-plus"></i>',                    'class' => "$base text-white bg-blue-600 hover:bg-blue-700"],
                'edit'     => ['icon' => '<i class="fa fa-edit"></i>',                    'class' => "$base text-white bg-green-600 hover:bg-green-700"],
                'delete'   => ['icon' => '<i class="fa fa-remove fas fa-trash"></i>',     'class' => "$base text-white bg-red-600 hover:bg-red-700"],
                'import'   => ['icon' => '<i class="fa fa-upload"></i>',                  'class' => $outline],
                'export'   => ['icon' => '<i class="fa fa-download"></i>',                'class' => $outline],
                'upload'   => ['icon' => '<i class="fa fa-upload"></i>',                  'class' => $outline],
                'download' => ['icon' => '<i class="fa fa-download"></i>',                'class' => $outline],
                'print'    => ['icon' => '<i class="fa fa-print"></i>',                   'class' => "$base text-white bg-red-600 hover:bg-red-700"],
                'button'   => ['icon' => '',                                              'class' => $outline],
            ];
        } else {
            $buttons = [
                'refresh'  => ['icon' => '<i class="fa fa-refresh fas fa-sync-alt"></i>', 'class' => 'btn btn-dark'],
                'add'      => ['icon' => '<i class="fa fa-plus"></i>', 'class' => 'btn btn-primary'],
                'edit'     => ['icon' => '<i class="fa fa-edit"></i>', 'class' => 'btn btn-success'],
                'delete'   => ['icon' => '<i class="fa fa-remove fas fa-trash"></i>', 'class' => 'btn btn-danger'],
                'import'   => ['icon' => '<i class="fa fa-upload"></i>', 'class' => 'btn btn-light'],
                'export'   => ['icon' => '<i class="fa fa-download"></i>', 'class' => 'btn btn-light'],
                'upload'   => ['icon' => '<i class="fa fa-upload"></i>', 'class' => 'btn btn-light'],
                'download' => ['icon' => '<i class="fa fa-download"></i>', 'class' => 'btn btn-light'],
                'print'    => ['icon' => '<i class="fa fa-print"></i>', 'class' => 'btn btn-danger'],
                'button'   => ['icon' => '', 'class' => 'btn btn-light'],
            ];
        }

        if (!is_array($this->mainActions) || empty($this->mainActions)) {
            return [];
        }

        $processed = [];
        foreach ($this->mainActions as $action) {
            if (isset($buttons[$action['name']])) {
                $button      = $buttons[$action['name']];
                $processed[] = [
                    'name'   => $action['name'] ?? '',
                    'id'     => $action['id'] ?? ($action['name'] ?? ''),
                    'method' => $action['method'] ?? ($action['name'] ?? ''),
                    'title'  => $action['title'] ?? '',
                    'url'    => $action['url'] ?? 'javascript:void(0)',
                    'target' => $action['target'] ?? '',
                    'class'  => isset($action['class'])
                        ? $button['class'] . ' ' . $action['class']
                        : $button['class'],
                    'icon'   => $action['icon'] ?? $button['icon'],
                ];
            }
        }

        return $processed;
    }

    public function setBulkActions(array $bulkActions): static
    {
        $this->bulkActions = $bulkActions;
        return $this;
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function setColumns(array $cols = []): static
    {
        $this->cols = $cols;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->cols;
    }

    public function getRows()
    {
        return $this->paginator;
    }

    public function getRecordsPerPageOptions(): array
    {
        return $this->recordsPerPage;
    }

    public function getPaginationStyle(): string
    {
        return $this->paginationStyle;
    }

    public function getRecordsPerPageStyle(): string
    {
        return $this->recordsPerPageStyle;
    }

    public function buildUrl(array $params): string
    {
        return $this->getUrl($params);
    }

    private function getUrl(array $data): string
    {
        $post   = $this->getPost();
        $params = [];

        if (count($post) >= 1) {
            foreach ($post as $key => $value) {
                $params[$key] = array_key_exists($key, $data) ? $data[$key] : $value;
            }
            foreach ($data as $field => $value) {
                if (!isset($params[$field])) {
                    $params[$field] = $value;
                }
            }
        } else {
            $params = $data;
        }

        return $this->baseUrl . '?' . http_build_query($params);
    }

    public function __destruct()
    {
        // Nothing
    }
}
