<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait FormRequestCraftTrait
{
    protected function initFormRequestTemplate()
    {
        $this -> module['form-request']['template'] = $this -> getFormRequestStubContent();
        $this -> module['form-request']['name'] = $this -> getModelName() .'FormRequest';

        $this -> makeDirectory(app_path('Http' . DIRECTORY_SEPARATOR . 'Requests'));

        return $this;
    }

    public function compileFormRequestTemplate()
    {
        $this -> initFormRequestTemplate()
                -> formRequestReplaceModelName()
                -> formRequestReplaceDatabaseTable()
                -> craftFormRequest();
    }

    // -- Gets the content of a stub
    protected function getFormRequestStubContent()
    {
        return File::get($this -> module['form-request']['stub']);
    }

    protected function getFormRequestTemplate()
    {
        return $this -> module['form-request']['template'];
    }

    protected function setFormRequestName($name)
    {
        $this -> module['form-request']['name'] = $name;
    }

    protected function getFormRequestName()
    {
        return $this -> module['form-request']['name'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function formRequestReplaceModelName()
    {
        $this->replaceTemplate(
            $this -> module['form-request']['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );

        return $this->replaceTemplate(
            $this -> module['form-request']['template'],
            $this -> getReplaceTemplate('route-model'),
            strtolower($this -> module['model']['name'])
        );
    }

    // -- Replaces the __DB_TABLE template
    protected function formRequestReplaceDatabaseTable()
    {
        return $this->replaceTemplate(
            $this -> module['form-request']['template'],
            $this -> getReplaceTemplate('db-table'),
            $this -> module['model']['db-table']
        );
    }

    protected function getFormRequestFileName()
    {
        return $this -> module['form-request']['name'];
    }

    protected function getFormRequestFilePath()
    {
        return app_path('Http'. DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this -> getFormRequestFileName() .'.php');
    }

    protected function craftFormRequest()
    {
        $this -> putContentInFile(
            $this -> getFormRequestFilePath(),
            $this -> getFormRequestTemplate()
        );
    }

    protected function uncraftFormRequest()
    {
        $this -> deleteFile($this -> getFormRequestFilePath());
    }
}
