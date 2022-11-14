<?php

namespace InfyOm\Generator\Generators\Scaffold;


use InfyOm\Generator\Generators\FormRequestGenerator;

class RequestGenerator extends FormRequestGenerator
{

    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->request;
        $this->createFileName = 'Create'.$this->config->modelNames->name.'Request.php';
        $this->updateFileName = 'Update'.$this->config->modelNames->name.'Request.php';
    }

    public function getViewCreateName(): string
    {
        return 'laravel-generator::scaffold.request.create';
    }

    public function getViewUpdateName(): string
    {
        return 'laravel-generator::scaffold.request.update';
    }
}
