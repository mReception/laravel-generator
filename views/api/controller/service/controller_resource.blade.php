@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiController }};

use {{ $config->namespaces->apiRequest }}\Create{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Update{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use {{ $config->namespaces->repository }}\{{ $config->modelNames->name }}Repository;
use {{ $config->namespaces->service }}\{{ $config->modelNames->name }}ManageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use {{ $config->namespaces->app }}\Http\Controllers\AppBaseController;
use {{ $config->namespaces->apiResource }}\{{ $config->modelNames->name }}Resource;

{!! $docController !!}
class {{ $config->modelNames->name }}APIController extends AppBaseController
{
    public function __construct(
        private readonly {{ $config->modelNames->name }}Repository ${{ $config->modelNames->camel }}Repository,
        private readonly {{ $config->modelNames->name }}ManageService ${{ $config->modelNames->camel }}ManageService
    ) {}

    {!! $docIndex !!}
    public function index(Request $request): JsonResponse
    {
        ${{ $config->modelNames->camelPlural }} = $this->{{ $config->modelNames->camel }}Repository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

@if($config->options->localized)
        return $this->sendResponse(
            {{ $config->modelNames->name }}Resource::collection(${{ $config->modelNames->camelPlural }}),
            __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.plural')])
        );
@else
        return $this->sendResponse({{ $config->modelNames->name }}Resource::collection(${{ $config->modelNames->camelPlural }}), '{{ $config->modelNames->humanPlural }} retrieved successfully');
@endif
    }

    {!! $docStore !!}
    public function store(Create{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}ManageService->create($input);

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.saved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->human }} saved successfully');
@endif
    }

    {!! $docShow !!}
    public function show($id): JsonResponse
    {
        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->human }} retrieved successfully');
@endif
    }

    {!! $docUpdate !!}
    public function update($id, Update{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}ManageService->update(${{ $config->modelNames->camel }}, $input);

@if($config->options->localized)
        return $this->sendResponse(
            new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}),
            __('messages.updated', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendResponse(new {{ $config->modelNames->name }}Resource(${{ $config->modelNames->camel }}), '{{ $config->modelNames->name }} updated successfully');
@endif
    }

    {!! $docDestroy !!}
    public function destroy($id): JsonResponse
    {
        /** @var {{ $config->modelNames->name }} ${{ $config->modelNames->camel }} */
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }}->delete();

@if($config->options->localized)
        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/{{ $config->modelNames->camelPlural }}.singular')])
        );
@else
        return $this->sendSuccess('{{ $config->modelNames->human }} deleted successfully');
@endif
    }
}
