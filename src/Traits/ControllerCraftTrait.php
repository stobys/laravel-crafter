<?php

namespace SylveK\LaravelCrafter\Traits;

trait ControllerCraftTrait
{
    protected function setControllerName($name)
    {
        $this -> module['controller']['name'] = $name;
    }

    protected function getControllerName()
    {
        return $this -> module['controller']['name'];
    }

    protected function initControllerTemplate()
    {
        $this -> module['controller']['template'] = $this -> getStubContent('controller');

        return $this;
    }

    public function compileControllerTemplate()
    {
        $this -> initControllerTemplate()
                -> replaceModelNamespace('controller')
                -> replaceModelName('controller')
                -> replaceControllerNamespace()
                -> replaceControllerName()
                -> replaceViewsDir()
                -> craftController();

        return $this -> getModelTemplate();
    }

    protected function getControllerTemplate()
    {
        return $this -> module['controller']['template'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function replaceControllerName()
    {
        return $this->replaceTemplate(
            $this -> module['controller']['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );
    }

    // -- Replaces the __MODEL_NAMESPACE template
    protected function getControllerNamespace()
    {
        return $this -> module['controller']['namespace'];
    }

    // -- Replaces the __MODEL_NAMESPACE template
    protected function replaceControllerNamespace()
    {
        return $this -> replaceTemplate(
            $this -> module['controller']['template'],
            $this -> getReplaceTemplate('controller-namespace'),
            $this -> getControllerNamespace()
        );
    }

    protected function getControllerFullPath()
    {
        $namespacePath = explode(DIRECTORY_SEPARATOR, $this -> convertNamespaceToDir($this -> getControllerNamespace()));
        array_shift($namespacePath);
        $namespacePath = implode(DIRECTORY_SEPARATOR, $namespacePath);

        return app_path()
                . DIRECTORY_SEPARATOR
                . $namespacePath
                . DIRECTORY_SEPARATOR
                . $this -> getControllerName()
                . 'Controller.php';
    }

    protected function craftController()
    {
        $this -> putContentInFile($this -> getControllerFullPath(), $this -> getControllerTemplate());
    }

    protected function uncraftController()
    {
        $this -> deleteFile($this -> getControllerFullPath());
    }
}
