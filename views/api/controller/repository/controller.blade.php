@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiController }};

use {{ $config->namespaces->apiRequest }}\Create{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Update{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use {{ $config->namespaces->repository }}\{{ $config->modelNames->name }}Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use {{ $config->namespaces->app }}\Http\Controllers\AppBaseController;

{!! $docController !!}
class {{ $config->modelNames->name }}APIController extends AppBaseController
{
    private {{ $config->modelNames->name }}Repository $repository;

    public function __construct({{ $config->modelNames->name }}Repository $repository)
    {
        $this->repository = $repository;
    }

    {!! $docIndex !!}
    public function index(Request $request): JsonResponse
    {
        if(empty($request->get('limit'))) {
            $result = $this->repository->all(
                $request->except(['skip', 'limit']),
                $request->get('skip'),
                $request->get('limit'),
                $request->get('columns')??['*']
            );
        } else {
            $result = $this->repository->paginate(
                $request->get('limit'),
                $request->get('columns')?? ['*'],
                $request->get('skip'),
                $request->get('search')??[],
                $request->get('sortBy'),
                $request->get('descending'),
                $request->get('filter')
            );
        }
    
    @if($config->options->localized)
        return $this->sendResponse(
        $result->toArray(),
        __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.plural')])
        );
    @else
        return $this->sendResponse($result->toArray(), '{{ $config->modelNames->humanPlural }} retrieved successfully');
    @endif
    }

    {!! $docStore !!}
    public function store(Create{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        ${{ $config->modelNames->camel }} = $this->repository->create($input);

@if($config->options->localized)
        return $this->sendResponse(
            ${{ $config->modelNames->camel }}->toArray(),
            __('messages.saved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(${{ $config->modelNames->camel }}->toArray(), '{{ $config->modelNames->human }} saved successfully');
@endif
    }

    {!! $docShow !!}
    public function show($id): JsonResponse
    {
        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/$MODEL_NAME_PLURAL_CAMEL$.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

@if($config->options->localized)
        return $this->sendResponse(
            ${{ $config->modelNames->camel }}->toArray(),
            __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(${{ $config->modelNames->camel }}->toArray(), '{{ $config->modelNames->human }} retrieved successfully');
@endif
    }

    {!! $docUpdate !!}
    public function update($id, Update{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/$MODEL_NAME_PLURAL_CAMEL$.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }} = $this->repository->update($input, $id);

@if($config->options->localized)
        return $this->sendResponse(
            ${{ $config->modelNames->camel }}->toArray(),
            __('messages.updated', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(${{ $config->modelNames->camel }}->toArray(), '{{ $config->modelNames->name }} updated successfully');
@endif
    }

    {!! $docDestroy !!}
    public function destroy($id): JsonResponse
    {
        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/$MODEL_NAME_PLURAL_CAMEL$.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }}->delete();

@if($config->options->localized)
        return $this->sendError(
            $id,
            __('messages.deleted', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendSuccess('{{ $config->modelNames->human }} deleted successfully');
@endif
    }
}
