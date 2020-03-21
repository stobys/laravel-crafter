<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait LangCraftTrait
{
    protected function initLangTemplate()
    {
        $this -> module['lang']['template'] = $this -> getLangStubContent();
        $this -> module['lang']['name'] = strtolower($this -> getControllerName());

        return $this;
    }

    public function compileLangTemplate()
    {
        $this -> initLangTemplate()
                -> langReplaceControllerName()
                -> langReplaceModelName()
                -> craftLang();
    }

    // -- Gets the content of a stub
    protected function getLangStubContent()
    {
        return File::get($this -> module['lang']['stub']);
    }

    protected function getLangTemplate()
    {
        return $this -> module['lang']['template'];
    }

    protected function getLangName()
    {
        return $this -> module['lang']['name'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function langReplaceModelName()
    {
        $this->replaceTemplate(
            $this -> module['lang']['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );

        return $this->replaceTemplate(
            $this -> module['lang']['template'],
            $this -> getReplaceTemplate('route-model'),
            strtolower($this -> module['model']['name'])
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function langReplaceControllerName()
    {
        $this->replaceTemplate(
            $this -> module['lang']['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );

        return $this->replaceTemplate(
            $this -> module['lang']['template'],
            $this -> getReplaceTemplate('route-controller'),
            strtolower($this -> module['controller']['name'])
        );
    }


    protected function getLangFileName()
    {
        return $this -> module['lang']['name'];
    }

    protected function getLangFilePath()
    {
        return resource_path('lang'. DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $this -> getLangFileName() .'.php');
    }

    protected function craftLang()
    {
        $this -> putContentInFile(
            $this -> getLangFilePath(),
            $this -> getLangTemplate()
        );
    }

    protected function uncraftLang()
    {
        $this -> deleteFile($this -> getLangFilePath());
    }
}
