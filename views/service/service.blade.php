@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->service }};

use App\Services\BaseService;
use {{ $config->namespaces->repository }}\{{ $config->modelNames->name }}Repository;

class {{ $config->modelNames->name }}ManageService extends BaseService
{
    public function repository(): string
    {
        return {{ $config->modelNames->name }}Repository::class;
    }

    /**
     * Store a newly created {{ $config->modelNames->name }} in storage.
     *
     * @return {{ $config->modelNames->name }}
     */
    public function create(array $input): /** @var {{ $config->modelNames->name }}
    {

        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = parent::create($input);

        return ${{ $config->modelNames->camel }};
    }

    /**
     * Update the specified {{ $config->modelNames->name }} in storage.
     *
     * @return {{ $config->modelNames->name }}
     */
    public function update({{ $config->modelNames->name }} $model, array $input): {{ $config->modelNames->name }}
    {
        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = parent::update($model, $input);

        return ${{ $config->modelNames->camel }};
    }

    /**
     * Remove the specified {{ $config->modelNames->name }} from storage.
     *
     * @throws \Exception
     */
    public function delete($id): void
    {
        parent::delete($model, $input);
    }
}
