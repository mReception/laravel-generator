<?php

namespace InfyOm\Generator\Generators;

abstract class FormRequestGenerator extends BaseGenerator
{

    protected ModelGenerator $modelGenerator;

    protected string $createFileName;

    protected string $updateFileName;

    public function __construct()
    {
        parent::__construct();

        $this->modelGenerator = new ModelGenerator();

    }
    /**
     * Specify View for rendering create request
     *
     * @return string
     */
    abstract public function getViewCreateName(): string;
    /**
     * Specify View for rendering update request
     *
     * @return string
     */
    abstract public function getViewUpdateName(): string;

    public function generate()
    {
        $this->generateCreateRequest();
        $this->generateUpdateRequest();
    }

    protected function generateCreateRequest(): void
    {
        $templateData = view($this->getViewCreateName(), [
            'enumRules' => $this->modelGenerator->generateFormRequestRules()
        ])->render();

        g_filesystem()->createFile($this->path.$this->createFileName, $templateData);

        $this->config->commandComment(infy_nl().'Create Request created: ');
        $this->config->commandInfo($this->createFileName);
    }

    protected function generateUpdateRequest()
    {

        $rules = $this->modelGenerator->generateUniqueRules();
        $templateData = view(
            $this->getViewUpdateName(),
            [
                'uniqueRules' => $this->modelGenerator->generateUniqueRules(),
                'enumRules' => $this->modelGenerator->generateFormRequestRules()
            ]
            )->render();

        g_filesystem()->createFile($this->path.$this->updateFileName, $templateData);

        $this->config->commandComment(infy_nl().'Update Request created: ');
        $this->config->commandInfo($this->updateFileName);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->createFileName)) {
            $this->config->commandComment('Create Request file deleted: '.$this->createFileName);
        }

        if ($this->rollbackFile($this->path, $this->updateFileName)) {
            $this->config->commandComment('Update Request file deleted: '.$this->updateFileName);
        }
    }

}
