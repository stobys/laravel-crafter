<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait RoutesCraftTrait
{
    protected function initRoutesTemplate()
    {
        $this -> module['routes']['template'] = $this -> getRoutesStubContent();
        $this -> module['routes']['name'] = strtolower($this -> getControllerName());

        $this -> makeDirectory(base_path('routes' . DIRECTORY_SEPARATOR . 'controllers'));

        return $this;
    }

    public function compileRoutesTemplate()
    {
        $this -> initRoutesTemplate()
                -> routesReplaceModelNamespace()
                -> routesReplaceModelName()
                -> routesReplaceControllerName()
                -> craftRoutes();
    }

    // -- Gets the content of a stub
    protected function getRoutesStubContent()
    {
        return File::get($this -> module['routes']['stub']);
    }

    protected function getRoutesTemplate()
    {
        return $this -> module['routes']['template'];
    }

    protected function getRoutesName()
    {
        return $this -> module['routes']['name'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function routesReplaceModelNamespace()
    {
        return $this->replaceTemplate(
            $this -> module['routes']['template'],
            $this -> getReplaceTemplate('model-namespace'),
            $this -> module['model']['namespace']
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function routesReplaceModelName()
    {
        $this->replaceTemplate(
            $this -> module['routes']['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );

        return $this->replaceTemplate(
            $this -> module['routes']['template'],
            $this -> getReplaceTemplate('route-model'),
            strtolower($this -> module['model']['name'])
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function routesReplaceControllerName()
    {
        $this->replaceTemplate(
            $this -> module['routes']['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );

        return $this->replaceTemplate(
            $this -> module['routes']['template'],
            $this -> getReplaceTemplate('route-controller'),
            strtolower($this -> module['controller']['name'])
        );
    }


    protected function getRoutesFileName()
    {
        return $this -> module['routes']['name'];
    }

    protected function getRoutesFilePath()
    {
        return base_path('routes'. DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $this -> getRoutesFileName() .'.php');
    }

    protected function craftRoutes()
    {
        $this -> putContentInFile(
            $this -> getRoutesFilePath(),
            $this -> getRoutesTemplate()
        );
    }
}
