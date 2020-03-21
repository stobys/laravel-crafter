<?php

namespace SylveK\LaravelCrafter\Traits;

trait ModelCraftTrait
{
    protected function setModelName($name)
    {
        $this -> module['model']['name'] = $name;
    }

    protected function getModelName()
    {
        return $this -> module['model']['name'];
    }

    protected function initModelTemplate()
    {
        $this -> module['model']['template'] = $this -> getStubContent('model');

        return $this;
    }

    public function compileModelTemplate()
    {
        $this -> initModelTemplate()
                -> replaceModelNamespace()
                -> replaceModelName()
                -> replaceDatabaseTable()
                -> craftModel();

        return $this -> getModelTemplate();
    }

    protected function getModelTemplate()
    {
        return $this -> module['model']['template'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function replaceModelName($where = 'model')
    {
        return $this->replaceTemplate(
            $this -> module[$where]['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );
    }

    // -- Replaces the __MODEL_NAMESPACE template
    protected function getModelNamespace()
    {
        return $this -> module['model']['namespace'];
    }

    // -- Replaces the __MODEL_NAMESPACE template
    protected function replaceModelNamespace($where = 'model')
    {
        return $this -> replaceTemplate(
            $this -> module[$where]['template'],
            $this -> getReplaceTemplate('model-namespace'),
            $this -> module['model']['namespace']
        );
    }

    protected function setDatabaseTable($table = 'default')
    {
        $this -> module['model']['db-table'] = $table;
    }

    protected function getDatabaseTable()
    {
        return $this -> module['model']['db-table'];
    }

    // -- Replaces the __DB_TABLE template
    protected function replaceDatabaseTable()
    {
        return $this->replaceTemplate(
            $this -> module['model']['template'],
            $this -> getReplaceTemplate('db-table'),
            $this -> getDatabaseTable()
        );
    }

    protected function getModelFullPath()
    {
        $namespacePath = explode(DIRECTORY_SEPARATOR, $this -> convertNamespaceToDir($this -> getModelNamespace()));
        array_shift($namespacePath);
        $namespacePath = implode(DIRECTORY_SEPARATOR, $namespacePath);

        return app_path()
                . DIRECTORY_SEPARATOR
                . $namespacePath
                . DIRECTORY_SEPARATOR
                . $this -> getModelName()
                . '.php';
    }

    protected function craftModel()
    {
        $this -> putContentInFile($this -> getModelFullPath(), $this -> getModelTemplate());
    }

    protected function uncraftModel()
    {
        $this -> deleteFile($this -> getModelFullPath());
    }
}
