<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait ObserverCraftTrait
{
    protected function initObserverTemplate()
    {
        $this -> module['observer']['template'] = $this -> getObserverStubContent();

        $this -> makeDirectory(app_path('Observers'));

        return $this;
    }

    public function compileObserverTemplate()
    {
        $this -> initObserverTemplate()
                -> observerReplaceModelNamespace()
                -> observerReplaceModelName()
                -> observerReplaceControllerName()
                -> craftObserver();

        $command = $this -> getModelName() .'::observe('. $this -> getObserverFileName() .'::class)';
        $this -> comment('    Please remember to add `'. $command .'` to AppServiceProvider@boot method');
    }

    // -- Gets the content of a stub
    protected function getObserverStubContent()
    {
        return File::get($this -> module['observer']['stub']);
    }

    protected function getObserverTemplate()
    {
        return $this -> module['observer']['template'];
    }

    protected function setObserverName($name)
    {
        $this -> module['observer']['name'] = $name;
    }

    protected function getObserverName()
    {
        return $this -> module['observer']['name'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function observerReplaceModelNamespace()
    {
        return $this->replaceTemplate(
            $this -> module['observer']['template'],
            $this -> getReplaceTemplate('model-namespace'),
            $this -> module['model']['namespace']
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function observerReplaceModelName()
    {
        $this->replaceTemplate(
            $this -> module['observer']['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );

        return $this->replaceTemplate(
            $this -> module['observer']['template'],
            $this -> getReplaceTemplate('route-model'),
            strtolower($this -> module['model']['name'])
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function observerReplaceControllerName()
    {
        $this->replaceTemplate(
            $this -> module['observer']['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );

        return $this->replaceTemplate(
            $this -> module['observer']['template'],
            $this -> getReplaceTemplate('route-controller'),
            strtolower($this -> module['controller']['name'])
        );
    }


    protected function getObserverFileName()
    {
        return $this -> module['observer']['name'];
    }

    protected function getObserverFilePath()
    {
        return app_path('Observers'. DIRECTORY_SEPARATOR . $this -> getObserverFileName() .'Observer.php');
    }

    protected function craftObserver()
    {
        $this -> putContentInFile(
            $this -> getObserverFilePath(),
            $this -> getObserverTemplate()
        );
    }

    protected function uncraftObserver()
    {
        $this -> deleteFile($this -> getObserverFilePath());
    }
}
