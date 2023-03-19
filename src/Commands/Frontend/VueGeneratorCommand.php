<?php

namespace InfyOm\Generator\Commands\Frontend;

use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Generators\Frontend\Vue\VueGenerator;

class VueGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'infyom:frontend.vue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD Vue frontend for given model';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
        $this->fireFileCreatingEvent('vue');

        if ($this->config->options->vue) {
            /** @var $modelGenerator VueGenerator */
            $modelGenerator = app(VueGenerator::class);
            $modelGenerator->generateVuePage();
            $modelGenerator->generateVueModel();
            $modelGenerator->generateVueComponent();
            $modelGenerator->generateVueStore();
            $modelGenerator->generateFormRequest();
            $modelGenerator->generateVueAxiosService();
            $modelGenerator->generateVueTableComponent();
            $modelGenerator->generateVueDbFields();
            $modelGenerator->generateVueFormComponent();
            $modelGenerator->generateVueFormDefaultComponent();

        }

        $this->fireFileCreatedEvent('vue');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }
}
