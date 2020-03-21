<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait ViewsCraftTrait
{
    protected function initViewTemplate($view = 'index')
    {
        $this -> module['views'][$view]['template'] = $this -> getViewsStubContent($view);

        return $this;
    }

    public function compileViewsTemplates()
    {
        $this -> makeDirectory($this -> getViewsPath());

        foreach ($this -> module['views'] as $view => $data) {
            $this -> initViewTemplate($view)
                    -> viewReplaceModelName($view)
                    -> viewReplaceControllerName($view)
                    -> viewReplaceViewsDir($view)
                    -> craftView($view);
        }
    }

    protected function setViewsDir($dir)
    {
        $this -> module['model']['views-dir'] = $dir;
    }

    protected function getViewsDir()
    {
        return $this -> module['model']['views-dir'];
    }

    protected function replaceViewsDir($where = 'controller')
    {
        return $this->replaceTemplate(
            $this -> module[$where]['template'],
            $this -> getReplaceTemplate('views-dir'),
            $this -> getViewsDir()
        );
    }

    // -- Gets the content of a stub
    protected function getViewsStubContent($view = 'index')
    {
        return File::get($this -> module['views'][$view]['stub']);
    }

    protected function getViewTemplate($view = 'index')
    {
        return $this -> module['views'][$view]['template'];
    }

    protected function getViewName($view = 'index')
    {
        return $this -> module['views'][$view]['name'];
    }

    // -- Replaces the __MODEL_NAME template
    protected function viewReplaceModelName($view = 'index')
    {
        $this->replaceTemplate(
            $this -> module['views'][$view]['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );

        return $this->replaceTemplate(
            $this -> module['views'][$view]['template'],
            $this -> getReplaceTemplate('route-model'),
            strtolower($this -> module['model']['name'])
        );
    }

    // -- Replaces the __CONTROLLER_NAME template
    protected function viewReplaceControllerName($view = 'index')
    {
        $this->replaceTemplate(
            $this -> module['views'][$view]['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );

        return $this->replaceTemplate(
            $this -> module['views'][$view]['template'],
            $this -> getReplaceTemplate('route-controller'),
            strtolower($this -> module['controller']['name'])
        );
    }

    protected function viewReplaceViewsDir($view = 'index')
    {
        return $this->replaceTemplate(
            $this -> module['views'][$view]['template'],
            $this -> getReplaceTemplate('views-dir'),
            $this -> getViewsDir()
        );
    }

    protected function getViewsPath()
    {
        return resource_path('views' . DIRECTORY_SEPARATOR . $this -> getViewsDir());
    }

    protected function getViewsFilePath($view = 'index')
    {
        return $this -> getViewsPath() . DIRECTORY_SEPARATOR . $this -> getViewName($view);
    }

    protected function craftView($view = 'index')
    {
        $this -> putContentInFile(
            $this -> getViewsFilePath($view),
            $this -> getViewTemplate($view)
        )
        ;
    }

    protected function uncraftViews()
    {
        // -- @TODO -- delete all files
        // $this -> deleteFile($this -> getViewsFilePath($view));
    }
}
