<?php 

namespace Pingu\Field\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;
use Pingu\Field\Support\FieldValidator\BundledEntityFieldsValidator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeEntityValidator extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-entity-validator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new entity validator class for the specified module.';

    public function getDefaultNamespace() : string
    {
        return $this->laravel['modules']->config('paths.generator.entity-validator.path', 'Entities/Validator');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the entity.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['bundled', null, InputOption::VALUE_NONE, 'Generates a validator for a bundled entity.', null],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        Stub::setBasePath(__DIR__ . '/stubs');

        $class = $this->getClass();
        if (!Str::endsWith($class, 'Validator')) {
            $class .= 'Validator';
        }

        return (new Stub("/entity-validator.stub", [
            'NAMESPACE'    => $this->getClassNamespace($module),
            'CLASS'        => $class,
            'EXTENDS'        => class_basename(BaseFieldsValidator::class)
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $entityPath = GenerateConfigReader::read('entity-validator');

        return $path . $entityPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        $name = Str::studly($this->argument('name'));
        if (!Str::endsWith($name, 'Validator')) {
            $name .= 'Validator';
        }
        return $name;
    }
}