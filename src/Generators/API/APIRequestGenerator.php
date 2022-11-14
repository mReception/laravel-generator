<?php

namespace InfyOm\Generator\Generators\API;

use InfyOm\Generator\Generators\FormRequestGenerator;


class APIRequestGenerator extends FormRequestGenerator
{

    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->apiRequest;
        $this->createFileName = 'Create'.$this->config->modelNames->name.'APIRequest.php';
        $this->updateFileName = 'Update'.$this->config->modelNames->name.'APIRequest.php';
    }

    public function getViewCreateName(): string
    {
        return 'laravel-generator::api.request.create';
    }

    public function getViewUpdateName(): string
    {
        return 'laravel-generator::api.request.update';
    }
}
