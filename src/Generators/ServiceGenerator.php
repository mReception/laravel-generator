<?php

namespace InfyOm\Generator\Generators;


class ServiceGenerator extends BaseGenerator
{
    /**
     * Fields not included in the generator by default.
     */
    protected array $excluded_fields = [];

    private string $fileName;

    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->service;
        $this->fileName = $this->config->modelNames->name.'ManageService.php';
    }

    public function generate()
    {
        $templateData = view('laravel-generator::service.service', $this->variables())->render();

        g_filesystem()->createFile($this->path.$this->fileName, $templateData);

        $this->config->commandComment(infy_nl().'Service created: ');
        $this->config->commandInfo($this->fileName);
    }

    public function variables(): array
    {
        return [

        ];
    }

}
