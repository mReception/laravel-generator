@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->apiController }};

use {{ $config->namespaces->apiRequest }}\Create{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->apiRequest }}\Update{{ $config->modelNames->name }}APIRequest;
use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
use {{ $config->namespaces->repository }}\{{ $config->modelNames->name }}Repository;
use {{ $config->namespaces->service }}\{{ $config->modelNames->name }}Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use {{ $config->namespaces->app }}\Http\Controllers\AppBaseController;

{!! $docController !!}
class {{ $config->modelNames->name }}APIController extends AppBaseController
{
    public function __construct(
        private readonly {{ $config->modelNames->name }}Repository ${{ $config->modelNames->camel }}Repository,
        private readonly {{ $config->modelNames->name }}Service ${{ $config->modelNames->camel }}Service
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
            ${{ $config->modelNames->camelPlural }}->toArray(),
            __('messages.retrieved', ['model' => __('models/{{ $config->modelNames->camelPlural }}.plural')])
        );
@else
        return $this->sendResponse(${{ $config->modelNames->camelPlural }}->toArray(), '{{ $config->modelNames->humanPlural }} retrieved successfully');
@endif
    }

    {!! $docStore !!}
    public function store(Create{{ $config->modelNames->name }}APIRequest $request): JsonResponse
    {
        $input = $request->all();

        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Service->create($input);
        $this->{{ $config->modelNames->camel }}Repository->save(${{ $config->modelNames->camel }});

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
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

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
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

        if (empty(${{ $config->modelNames->camel }})) {
@if($config->options->localized)
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/$MODEL_NAME_PLURAL_CAMEL$.singular')])
            );
@else
            return $this->sendError('{{ $config->modelNames->human }} not found');
@endif
        }

        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Service->update($input, $id);
        $this->{{ $config->modelNames->camel }}Repo->save(${{ $config->modelNames->camel }});

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
        ${{ $config->modelNames->camel }} = $this->{{ $config->modelNames->camel }}Repository->find($id);

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
