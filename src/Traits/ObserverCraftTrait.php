<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait ObserverCraftTrait
{
    protected function initObserverTemplate()
    {
        $this -> module['observer']['template'] = $this -> getObserverStubContent();
        $this -> module['observer']['name'] = strtolower($this -> getControllerName());

        $this -> makeDirectory(base_path('routes' . DIRECTORY_SEPARATOR . 'controllers'));

        return $this;
    }

    public function compileObserverTemplate()
    {
        $this -> initObserverTemplate()
                -> observerReplaceModelNamespace()
                -> observerReplaceModelName()
                -> observerReplaceControllerName()
                -> craftObserver();

        $command = $this -> getModelName() .'::observe('. $this -> getModelName() .'Observer::class)';
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
        return app_path('Observers'. DIRECTORY_SEPARATOR . $this -> getObserverFileName() .'.php');
    }

    protected function craftObserver()
    {
        $this -> putContentInFile(
            $this -> getObserverFilePath(),
            $this -> getObserverTemplate()
        );
    }
}
