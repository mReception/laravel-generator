@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $namespaceApp }}Services;

use {{ $namespaceApp }}Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    /**
     * @var BaseRepository
     */
    protected BaseRepository $repository;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->makeRepository();
    }

    /**
     * Configure the Repository
     */
    abstract public function repository(): string;

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return BaseRepository
     */
    public function makeRepository(): BaseRepository
    {
        $repository = app($this->repository());

        if (!$repository instanceof BaseRepository) {
            throw new \RuntimeException("Class {$this->repository()} must be an instance of {{ $namespaceApp }}\Repository\\BaseRepository");
        }

        return $this->repository = $repository;
    }

    /**
     * Create model record
     *
     * @param array $input
     * @return Model
     */
    public function create(array $input): Model
    {
        return $this->repository->create($input);
    }


    /**
     * Update model record for given id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update(array $input, int $id): Model
    {
        return $this->repository->update($input, $id);
    }

    /**
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete(int $id): ?bool
    {
        return $this->repository->findOrFail($id)->delete();
    }
}