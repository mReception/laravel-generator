<?php

namespace InfyOm\Generator\Generators\Frontend\Vue;


use Illuminate\Support\Str;
use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Generators\SwaggerGenerator;
use InfyOm\Generator\Utils\TableFieldsGenerator;

class VueGenerator extends BaseGenerator
{

    private string $componentFileName;
    private string $componentTableName;
    private string $componentFormName;
    private string $componentDbFieldName;
    private string $modelFileName;
    private string $storeFileName;
    private string $serviceFileName;
    private string $requestFileName;
    /**
     * Fields not included in the generator by default.
     */
    protected array $excluded_fields = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];



    public function __construct()
    {
        parent::__construct();

        $this->path = $this->config->paths->vue;
        $this->componentFileName = $this->path . 'src/components/' . $this->config->modelNames->name . '.ts';
        $this->componentFormName = $this->path . 'src/components/' . $this->config->modelNames->name . 'FormComponent.vue';
        $this->componentTableName = $this->path . 'src/components/Table' . $this->config->modelNames->name . '.vue';
        $this->componentDbFieldName = $this->path . 'src/use/dbConst/' . $this->config->modelNames->dashed . '.ts';
        $this->modelFileName = $this->path . 'src/models/' .$this->config->modelNames->dashed . '.ts';
        $this->storeFileName = $this->path . 'src/store/modules/' .$this->config->modelNames->dashed . '.ts';
        $this->serviceFileName = $this->path . 'src/services/' .$this->config->modelNames->dashed . '.service.ts';
        $this->requestFileName = $this->path . 'src/models/requets/' .$this->config->modelNames->name . 'FormRequest.ts';

    }

    public function generateVueModel()
    {
        $templateData = view('laravel-generator::vue.model', $this->variables())->render();

        g_filesystem()->createFile($this->modelFileName, $templateData);

        $this->config->commandComment(infy_nl() . 'Vue Model created: ');
        $this->config->commandInfo($this->modelFileName);

    }

    public function generateVueComponent()
    {
        $templateData = view('laravel-generator::vue.component', $this->variables())->render();

        g_filesystem()->createFile($this->componentFileName, $templateData);

        $this->config->commandComment(infy_nl() . 'Component Vue created: ');
        $this->config->commandInfo($this->componentFileName);
    }
    public function generateVueFormComponent()
    {
        $templateData = view('laravel-generator::vue.form-dialog', $this->variables())->render();

        g_filesystem()->createFile($this->componentFormName, $templateData);

        $this->config->commandComment(infy_nl() . 'Component Form dialog Vue created: ');
        $this->config->commandInfo($this->componentFormName);
    }
    public function generateVueTableComponent()
    {
        $templateData = view('laravel-generator::vue.table', $this->variables())->render();

        g_filesystem()->createFile($this->componentTableName, $templateData);

        $this->config->commandComment(infy_nl() . 'Component Table Vue created: ');
        $this->config->commandInfo($this->componentTableName);
    }
    public function generateVueDbFields()
    {
        $templateData = view('laravel-generator::vue.dbFields', $this->variables())->render();

        g_filesystem()->createFile($this->componentDbFieldName, $templateData);

        $this->config->commandComment(infy_nl() . 'Component dbFields Vue created: ');
        $this->config->commandInfo($this->componentDbFieldName);
    }

    public function generateVueStore()
    {
        $templateData = view('laravel-generator::vue.store', $this->variables())->render();

        g_filesystem()->createFile($this->storeFileName, $templateData);

        $this->config->commandComment(infy_nl() . 'Store Vue created: ');
        $this->config->commandInfo($this->storeFileName);
    }

    public function generateFormRequest()
    {
        $templateData = view('laravel-generator::vue.request', $this->variables())->render();

        g_filesystem()->createFile($this->requestFileName, $templateData);

        $this->config->commandComment(infy_nl() . 'FormRequest Vue created: ');
        $this->config->commandInfo($this->requestFileName);
    }

    public function generateVueAxiosService()
    {
        $templateData = view('laravel-generator::vue.service', $this->variables())->render();

        g_filesystem()->createFile($this->serviceFileName, $templateData);

        $this->config->commandComment(infy_nl() . 'Service Vue created: ');
        $this->config->commandInfo($this->serviceFileName);
    }

    public function variables(): array
    {
        return [
            'fillables' => implode(',' . infy_nl_tab(1, 2), $this->generateFillables()),
            'properties' => $this->generateProperties(),
            'casts' => implode(',' . infy_nl_tab(1, 2), $this->generateCasts()),
            'rules' => implode(',' . infy_nl_tab(1, 2), $this->generateRules()),
            'swaggerDocs' => $this->fillDocs(),
            'customPrimaryKey' => $this->customPrimaryKey(),
            'customCreatedAt' => $this->customCreatedAt(),
            'customUpdatedAt' => $this->customUpdatedAt(),
            'customSoftDelete' => $this->customSoftDelete(),
            'relations' => $this->generateRelations(),
            'relationsDocProperties' => $this->generateRelationsDocProperties(),
            'timestamps' => config('laravel_generator.timestamps.enabled', true),
        ];
    }

    protected function customPrimaryKey()
    {
        $primary = $this->config->getOption('primary');

        if (!$primary) {
            return null;
        }

        if ($primary === 'id') {
            return null;
        }

        return $primary;
    }

    protected function customSoftDelete()
    {
        $deletedAt = config('laravel_generator.timestamps.deleted_at', 'deleted_at');

        if ($deletedAt === 'deleted_at') {
            return null;
        }

        return $deletedAt;
    }

    protected function customCreatedAt()
    {
        $createdAt = config('laravel_generator.timestamps.created_at', 'created_at');

        if ($createdAt === 'created_at') {
            return null;
        }

        return $createdAt;
    }

    protected function customUpdatedAt()
    {
        $updatedAt = config('laravel_generator.timestamps.updated_at', 'updated_at');

        if ($updatedAt === 'updated_at') {
            return null;
        }

        return $updatedAt;
    }

    protected function generateFillables(): array
    {
        $fillables = [];
        if (isset($this->config->fields) && !empty($this->config->fields)) {
            foreach ($this->config->fields as $field) {
                if ($field->isFillable) {
                    $fillables[] = "'" . $field->name . "'";
                }
            }
        }

        return $fillables;
    }

    protected function fillDocs(): string
    {
        if (!$this->config->options->swagger) {
            return '';
        }

        return $this->generateSwagger();
    }

    public function generateSwagger(): string
    {
        $requiredFields = $this->generateRequiredFields();

        $fieldTypes = SwaggerGenerator::generateTypes($this->config->fields);

        $properties = [];
        foreach ($fieldTypes as $fieldType) {
            $properties[] = view(
                'swagger-generator::model.property',
                $fieldType
            )->render();
        }

        $requiredFields = '{' . implode(',', $requiredFields) . '}';

        return view('swagger-generator::model.model', [
            'requiredFields' => $requiredFields,
            'properties' => implode(',' . infy_nl() . ' ', $properties),
        ]);
    }

    protected function generateRequiredFields(): array
    {
        $requiredFields = [];

        if (isset($this->config->fields) && !empty($this->config->fields)) {
            foreach ($this->config->fields as $field) {
                if (!empty($field->validations)) {
                    if (Str::contains($field->validations, 'required')) {
                        $requiredFields[] = '"' . $field->name . '"';
                    }
                }
            }
        }

        return $requiredFields;
    }

    protected function generateRules(): array
    {
        $dont_require_fields = config('laravel_generator.options.hidden_fields', [])
            + config('laravel_generator.options.excluded_fields', $this->excluded_fields);

        $rules = [];

        foreach ($this->config->fields as $field) {
            if (!$field->isPrimary && !in_array($field->name, $dont_require_fields)) {
                if ($field->isNotNull && empty($field->validations)) {
                    $field->validations = 'required';
                }
                $dbType = strtolower($field->dbType);
                $dbTypeValue = (str_contains($dbType, ',')) ? explode(',', $dbType)[0] : $dbType;
                /**
                 * Generate some sane defaults based on the field type if we
                 * are generating from a database table.
                 */
                if ($this->config->getOption('fromTable')) {
                    $rule = empty($field->validations) ? [] : explode('|', $field->validations);

                    if (!$field->isNotNull && !$field->isEnum) {
                        $rule[] = 'nullable';
                    }

                    switch ($dbTypeValue) {
                        case 'integer':
                        case 'increments':
                        case 'smallint':
                        case 'long':
                        case 'bigint':
                        case 'biginteger':
                            $rule[] = 'integer';
                            break;
                        case 'tinyint':
                        case 'boolean':
                            $rule[] = 'boolean';
                            break;
                        case 'float':
                        case 'double':
                        case 'decimal':
                            $rule[] = 'numeric';
                            break;
                        case 'enum':
                            $field->requestValidator = 'Rule::in(["' . implode('","', $field->htmlValues) . '"])';
                            $rule[] = 'string';
                            break;
                        case 'string':
                            $rule[] = 'string';
                            if ($field->length) {
                                $rule[] = 'max:' . $field->length;
                            } else {
                                // Enforce a maximum string length if possible.
                                foreach (explode(':', $field->dbType) as $key => $value) {
                                    if (preg_match('/string,(\d+)/', $value, $matches)) {
                                        $rule[] = 'max:' . $matches[1];
                                    }
                                }
                            }
                            break;
                        case 'text':
                            $rule[] = 'string';
                            break;
                    }

                    $field->validations = implode('|', $rule);
                    $field->requestValidator = $field->requestValidator ?? $field->validations;
                }
            }

            if (!empty($field->validations)) {
                if (Str::contains($field->validations, 'unique:')) {
                    $rule = explode('|', $field->validations);
                    // move unique rule to last
                    usort($rule, function ($record) {
                        return (Str::contains($record, 'unique:')) ? 1 : 0;
                    });
                    $field->validations = implode('|', $rule);
                }
                $rule = "'" . $field->name . "' => '" . $field->validations . "'";
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function generateFormRequestRules(): string
    {
        $dont_require_fields = config('laravel_generator.options.hidden_fields', [])
            + config('laravel_generator.options.excluded_fields', $this->excluded_fields);

        $enumRules = '';

        foreach ($this->config->fields as $field) {
            if (!$field->isPrimary && !in_array($field->name, $dont_require_fields, true)) {
                $dbType = strtolower($field->dbType);
                $dbTypeValue = (str_contains($dbType, ',')) ? explode(',', $dbType)[0] : $dbType;
                if ($dbTypeValue === 'enum' && $this->config->getOption('fromTable')) {

                    $enumRules .= '$rules["' . $field->name . '"] = [';

                    if ($field->isNotNull && empty($field->validations)) {
                        $enumRules .= '"required", ';
                    }

                    $enumRules .= 'Rule::in(["' . implode('","', $field->htmlValues) . '"]),';

                    $enumRules .= '];' . PHP_EOL;
                }
            }
        }

        return $enumRules;
    }

    public function generateUniqueRules(): string
    {
        $tableNameSingular = Str::singular($this->config->tableName);
        $uniqueRules = '';
        foreach ($this->generateRules() as $rule) {
            if (Str::contains($rule, 'unique:')) {
                $rule = explode('=>', $rule);
                $string = '$rules[' . trim($rule[0]) . '].","';

                $uniqueRules .= '$rules[' . trim($rule[0]) . '] = ' . $string . '.$this->route("' . $tableNameSingular . '");';
            }
        }

        return $uniqueRules;
    }

    public function generateCasts(): array
    {
        $casts = [];

        $timestamps = TableFieldsGenerator::getTimestampFieldNames();

        foreach ($this->config->fields as $field) {
            if (in_array($field->name, $timestamps)) {
                continue;
            }

            $rule = "'" . $field->name . "' => ";
            $dbType = strtolower($field->dbType);
            $dbTypeValue = (str_contains($dbType, ',')) ? explode(',', $dbType)[0] : $dbType;
            switch ($dbTypeValue) {
                case 'integer':
                case 'bigInteger':
                case 'increments':
                case 'smallint':
                case 'long':
                case 'bigint':
                case 'biginteger':
                    $rule .= "'integer'";
                    break;
                case 'double':
                    $rule .= "'double'";
                    break;
                case 'decimal':
                    $rule .= sprintf("'decimal:%d'", $field->numberDecimalPoints);
                    break;
                case 'float':
                    $rule .= "'float'";
                    break;
                case 'boolean':
                    $rule .= "'boolean'";
                    break;
                case 'datetime':
                case 'datetimetz':
                    $rule .= "'datetime'";
                    break;
                case 'date':
                    $rule .= "'date'";
                    break;
                case 'enum':
                case 'string':
                case 'char':
                case 'text':
                    $rule .= "'string'";
                    break;
                default:
                    $rule = '';
                    break;
            }

            if (!empty($rule)) {
                $casts[] = $rule;
            }
        }

        return $casts;
    }

    protected function generateRelations(): string
    {
        $relations = [];

        $count = 1;
        $fieldsArr = [];
        if (isset($this->config->relations) && !empty($this->config->relations)) {
            foreach ($this->config->relations as $relation) {
                $field = (isset($relation->inputs[0])) ? $relation->inputs[0] : null;

                $relationShipText = $field;
                if (in_array($field, $fieldsArr)) {
                    $relationShipText = $relationShipText . '_' . $count;
                    $count++;
                }

                $relationText = $relation->getRelationFunctionText($relationShipText);
                if (!empty($relationText)) {
                    $fieldsArr[] = $field;
                    $relations[] = $relationText;
                }
            }
        }

        return implode(infy_nl_tab(2), $relations);
    }

    protected function generateRelationsDocProperties(): string
    {
        $relations = [];

        if (isset($this->config->relations) && !empty($this->config->relations)) {
            foreach ($this->config->relations as $relation) {
                $relationPropertyText = $relation->getRelationDocPropertyText();
                $relations[] = $relationPropertyText;
            }
        }

        return implode(infy_nl_tab(1), $relations);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->config->commandComment('Model file deleted: ' . $this->fileName);
        }
    }

    /**
     * Generates the properties for the specified table.
     * @return array the generated properties (property => type)
     */
    protected function generateProperties()
    {
        $properties = [];
        $jsType = 'string';
        $jsFormType = null;

        foreach ($this->config->fields as $field) {
            $jsImport = null;
            $jsType = 'string';
            $jsName = $field->name;
            $dbType = strtolower($field->dbType);
            $dbTypeValue = (str_contains($dbType, ',')) ? explode(',', $dbType)[0] : $dbType;
            switch ($dbTypeValue) {
                case 'integer':
                case 'bigInteger':
                case 'increments':
                case 'smallint':
                case 'long':
                case 'bigint':
                case 'biginteger':
                    $type = 'int';
                    $jsType = 'number';
                    break;
                case 'double':
                    $type = 'double';
                    $jsType = 'number';
                    break;
                case 'decimal':
                    $type = sprintf("'decimal:%d'", $field->numberDecimalPoints);
                    $jsType = 'number';
                    break;
                case 'float':
                    $type = 'float';
                    $jsType = 'number';
                    break;
                case 'boolean':
                    $type = 'boolean';
                    $jsType = 'boolean';
                    break;
                case 'timestamp':
                case 'datetime':
                case 'datetimetz':
                case 'date':
                    $type = 'string';
                    $jsType = 'Date';
                    break;
                case 'enum':
                case 'string':
                case 'varchar':
                case 'email':
                case 'char':
                case 'text':
                    $type = 'string';
                    $jsType = 'string';
                    break;
                default:
                    $type = '';
                    $jsType = 'string';
                    break;
            }

            if (str_ends_with($field->name, '_id')) {
                $jsFormType = $jsType;
                $jsType = $field->getLabel();
                $jsName = $field->getJsName();
                $jsFileModelName = $field->getFileModelName();
                $jsImport = 'import { ' . $jsType . ' } from \'src/models/' . $jsFileModelName . '\'';
            }

            $properties[$field->name] = [
                'label' => $dbTypeValue ==='enum'?'select':null,
                'filter_type' => $dbTypeValue ==='enum' || str_ends_with($field->name,'_id')?'select':(($jsType==='Date')?'date':null),
                'multiple' => $dbTypeValue ==='enum' || str_ends_with($field->name,'_id')?'true':null,
                'options' => $dbTypeValue ==='enum'?'[]':null,
                'type' => $type,
                'js_type' => $jsType,
                'js_form_type' => $jsFormType ?? $jsType,
                'js_name' => $jsName,
                'js_import' => $jsImport ?? '',
                'name' => $field->name . '/*' . $field->dbType . '*/',
                'field_name' => $field->name,
                'row_name' => $field->name,
                'camel_plural' => Str::camel(Str::plural($jsName)),
                'name_plural' => Str::plural($jsName),
                'name_plural_title' => Str::title(Str::plural($jsName)),
                'human' => $field->getHuman()

            ];

        }

        return $properties;
    }

}
