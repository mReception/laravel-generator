@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->service }};

use App\Services\BaseService;
use {{ $config->namespaces->repository }}\{{ $config->modelNames->name }}Repository;
use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};

class {{ $config->modelNames->name }}ManageService extends BaseService
{
    public function repository(): string
    {
        return {{ $config->modelNames->name }}Repository::class;
    }

    /**
     * Store a newly created {{ $config->modelNames->name }} in storage.
     *
     * @param  array $input
     *
     * @return {{ $config->modelNames->name }}
     */
    public function create(array $input): {{ $config->modelNames->name }}
    {
        return parent::create($input);
    }

    /**
     * Update the specified {{ $config->modelNames->name }} in storage.
     *
     * @param  array $input
     * @param int $id
     *
     * @return {{ $config->modelNames->name }}
     */
    public function update(array $input, int $id): {{ $config->modelNames->name }}
    {
        return parent::update($input, $id);
    }

    /**
     * Remove the specified {{ $config->modelNames->name }} from storage.
     * @param int $id
     *
     * @return bool|mixed|null
     *
     * @throws \Exception
     */
    public function delete(int $id): bool|null
    {
        return parent::delete($id);
    }
}
