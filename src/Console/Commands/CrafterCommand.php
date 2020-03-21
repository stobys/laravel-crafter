<?php

namespace SylveK\LaravelCrafter\Console\Commands;

use Illuminate\Console\Command;

use SylveK\LaravelCrafter\Traits\ModelCraftTrait;
use SylveK\LaravelCrafter\Traits\ControllerCraftTrait;
use SylveK\LaravelCrafter\Traits\ViewsCraftTrait;
use SylveK\LaravelCrafter\Traits\DatabaseCraftTrait;
use SylveK\LaravelCrafter\Traits\FormRequestCraftTrait;
use SylveK\LaravelCrafter\Traits\RoutesCraftTrait;
use SylveK\LaravelCrafter\Traits\LangCraftTrait;
use SylveK\LaravelCrafter\Traits\ObserverCraftTrait;

use File;
use Str;

class CrafterCommand extends Command
{
    use ModelCraftTrait;
    use ControllerCraftTrait;
    use FormRequestCraftTrait;
    use DatabaseCraftTrait;
    use ViewsCraftTrait;
    use RoutesCraftTrait;
    use LangCraftTrait;
    use ObserverCraftTrait;

    // -- The name and signature of the console command.
    protected $signature = 'craft:module
                            {model : Singular module name to create}
                            {--purge : option to delete module}';

    // -- The console command description.
    protected $description = 'Creates module (model, controller, views, migration, seeder, factory, etc)';

    protected $replaceTemplates = [
        'model'                 => '__MODEL_NAME',
        'route-model'           => '__ROUTE_MODEL_NAME',
        'model-namespace'       => '__MODEL_NAMESPACE',
        'controller'            => '__CONTROLLER_NAME',
        'route-controller'      => '__ROUTE_CONTROLLER_NAME',
        'controller-namespace'  => '__CONTROLLER_NAMESPACE',
        'views-dir'             => '__VIEWS_DIR',
        'db-table'              => '__DB_TABLE',
    ];

    // --
    protected $module = [
        'model'         => [
            'namespace' => 'App\Models',
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/model.stub',
            'db-table'  => 'default',
            'views-dir' => 'default',
            'template'  => '',
        ],
        'controller'    => [
            'namespace' => 'App\Http\Controllers',
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/controller.stub',
            'template'  => '',
        ],
        'form-request'    => [
            'namespace' => 'App\Http\Requests',
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/model_form_request.stub',
            'template'  => '',
        ],
        'observer'    => [
            'namespace' => 'App\Observers',
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/observer.stub',
            'template'  => '',
        ],
        'routes'    => [
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/routes.stub',
            'template'  => '',
        ],
        'lang'    => [
            'name'      => null,
            'stub'      => __DIR__ .'/../../stubs/lang.stub',
            'template'  => '',
        ],
        'views'         => [
            'index' => [
                'stub'      => __DIR__ .'/../../stubs/view_index.blade.stub',
                'name'      => 'index.blade.php',
                'template'  => '',
            ],
            'index-row' => [
                'stub'      => __DIR__ .'/../../stubs/view_index-row.blade.stub',
                'name'      => 'index-row.blade.php',
                'template'  => '',
            ],
            'create' => [
                'stub'      => __DIR__ .'/../../stubs/view_create.blade.stub',
                'name'      => 'create.blade.php',
                'template'  => '',
            ],
            'edit' => [
                'stub'      => __DIR__ .'/../../stubs/view_edit.blade.stub',
                'name'      => 'edit.blade.php',
                'template'  => '',
            ],
            'create-edit' => [
                'stub'      => __DIR__ .'/../../stubs/view__form_create_edit.blade.stub',
                'name'      => '_form_create_edit.blade.php',
                'template'  => '',
            ],
            'filter' => [
                'stub'      => __DIR__ .'/../../stubs/view__form_filter.blade.stub',
                'name'      => '_form_filter.blade.php',
                'template'  => '',
            ],
            'show' => [
                'stub'      => __DIR__ .'/../../stubs/view_show.blade.stub',
                'name'      => 'show.blade.php',
                'template'  => '',
            ],
        ],
        'database'  => [
            'migration'    => [
                'stub'      => __DIR__ .'/../../stubs/db_migration.stub',
                'name'      => 'ModelMigration',
                'template'  => '',
            ],
            'factory'    => [
                'stub'      => __DIR__ .'/../../stubs/db_factory.stub',
                'name'      => 'ModelFactory',
                'template'  => '',
            ],
            'seeder'    => [
                'stub'      => __DIR__ .'/../../stubs/db_seeder.stub',
                'name'      => 'ModelSeeder',
                'template'  => '',
            ],
        ],
    ];

    // -- Execute the console command.
    public function handle()
    {
        $this -> initModule($this -> argument('model'));

        $purge = $this -> option('purge');
        switch ($purge) {
            case true:
                $this -> uncraftModuleFiles();
            break;

            case false:
                $this -> craftModuleFiles();
            break;
        }
    }

    protected function craftModuleFiles()
    {
        // -- compile template and make model
        $this -> compileModelTemplate();

        // -- compile template and make controller
        $this -> compileControllerTemplate();

        // -- compile form request template and make form request
        $this -> compileFormRequestTemplate();

        // -- compile routes template and make routes
        $this -> compileRoutesTemplate();

        // -- compile views templates and make views
        $this -> compileLangTemplate();

        // -- compile views templates and make views
        $this -> compileViewsTemplates();

        // -- compile database files (migration, factory, seeder)
        $this -> compileDatabaseTemplates();

        // -- compile observer class
        $this -> compileObserverTemplate();

        // $this -> makeEvents();

        $this->comment('    Module "'. $this -> getModelName() .'"" crafted!');
    }

    protected function uncraftModuleFiles()
    {
        $this -> comment(' > This will purge module files!');
        return null;

        // -- purge model
        $this -> uncraftModel();

        // -- purge controller
        $this -> uncraftController();

        // -- purge form request
        $this -> uncraftFormRequest();

        // -- purge routes
        $this -> uncraftRoutes();

        // -- purge lang
        $this -> uncraftLang();

        // -- purge views
        $this -> uncraftViews();

        // -- purge database files (migration, factory, seeder)
        $this -> uncraftDatabase();

        // -- purge observer class
        $this -> uncraftObserver();

        $this->comment('    Module "'. $this -> getModelName() .'"" purged!');
    }

    protected function initModule($model)
    {
        $models = Str::plural($model);

        $studlyModel = Str::studly($model);
        $studlyModels = Str::studly($models);

        $lowerModel = strtolower($model);
        $lowerModels = strtolower($models);

        $this -> setModelName($studlyModel);
        $this -> setControllerName($studlyModels);
        $this -> setDatabaseTable($lowerModels);
        $this -> setViewsDir($lowerModels);

        // -- to check and verify
        $this -> setRoutesName($lowerModels);
        $this -> setFormRequestName($studlyModel);
        $this -> setLangName($lowerModels);
        $this -> setObserverName($studlyModel);
    }



    protected function getReplaceTemplate($name)
    {
        return $this -> replaceTemplates[$name];
    }

    // -- Replaces a template variable in the given subject.
    protected function replaceTemplate(&$subject, $template, $value)
    {
        $subject = str_replace($template, $value, $subject);

        return $this;
    }



    // -- Build the directory for the class if necessary.
    protected function makeDirectory($path)
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    // -- Delete directory and files inside it.
    protected function deleteDirectory($path)
    {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
    }

    // -- Gets the content of a stub
    protected function getStubContent($name)
    {
        return File::get($this -> module[$name]['stub']);
    }

    // -- Determine if a file already exists.
    protected function isFileExists($file)
    {
        return File::exists($file);
    }

    // -- Get the given file content.
    protected function getFileContent($file)
    {
        return File::get($file);
    }

    // -- Delete the given file.
    protected function deleteFile($file)
    {
        return File::delete($file);
    }

    // -- Puts content to a given file.
    protected function putContentInFile($file, $content)
    {
        $path = dirname($file);

        if (!$this->isFileExists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        File::put($file, $content);

        return $this;
    }

    // -- Adds content to a given file.
    protected function appendContentToFile($file, $content)
    {
        File::append($file, $content);

        return $this;
    }

    protected function convertNamespaceToDir($namespace)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }
}
