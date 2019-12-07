<?php 

namespace Pingu\Field\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeEntityFields extends GeneratorCommand
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
    protected $name = 'module:make-entity-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new entity fields class for the specified module.';

    public function getDefaultNamespace() : string
    {
        return $this->laravel['modules']->config('paths.generator.entity-fields.path', 'Entities/Fields');
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
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        Stub::setBasePath(__DIR__ . '/stubs');

        $class = $this->getClass();
        if (!Str::endsWith($class, 'Fields')) {
            $class .= 'Fields';
        }

        return (new Stub(
            "/entity-fields.stub", [
            'NAMESPACE'    => $this->getClassNamespace($module),
            'CLASS'        => $class,
            ]
        ))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $entityPath = GenerateConfigReader::read('entity-fields');

        return $path . $entityPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        $name = Str::studly($this->argument('name'));
        if (!Str::endsWith($name, 'Fields')) {
            $name .= 'Fields';
        }
        return $name;
    }
}