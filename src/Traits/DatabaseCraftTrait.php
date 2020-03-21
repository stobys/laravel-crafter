<?php

namespace SylveK\LaravelCrafter\Traits;

use File;

trait DatabaseCraftTrait
{
    protected function initDatabaseTemplate($file = 'migration')
    {
        $this -> module['database'][$file]['template'] = $this -> getDatabaseStubContent($file);

        switch ($file) {
            case 'migration':
                $this -> module['database']['migration']['name'] = date('Y_m_d_His') .'_create_'. $this -> getDatabaseTable() .'_table';
                $this -> makeDirectory(database_path('migrations'));
            break;

            case 'factory':
                $this -> module['database']['factory']['name'] = $this -> getFactoryName();
                $this -> makeDirectory(database_path('factories'));
            break;

            case 'seeder':
                $this -> module['database']['seeder']['name'] = $this -> getControllerName() .'TableSeeder';
                $this -> makeDirectory(database_path('seeds'));
            break;
        }

        return $this;
    }

    public function compileDatabaseTemplates()
    {
        $this -> initDatabaseTemplate('migration')
                -> databaseReplaceModelNamespace('migration')
                -> databaseReplaceModelName('migration')
                -> databaseReplaceControllerName('migration')
                -> databaseReplaceDatabaseTable('migration')
                -> craftDatabase('migration');

        $this -> initDatabaseTemplate('factory')
                -> databaseReplaceModelNamespace('factory')
                -> databaseReplaceModelName('factory')
                -> databaseReplaceControllerName('factory')
                -> databaseReplaceDatabaseTable('factory')
                -> craftDatabase('factory');

        $this -> initDatabaseTemplate('seeder')
                -> databaseReplaceModelNamespace('seeder')
                -> databaseReplaceModelName('seeder')
                -> databaseReplaceControllerName('seeder')
                -> databaseReplaceDatabaseTable('seeder')
                -> craftDatabase('seeder');
    }

    // -- Gets the content of a stub
    protected function getDatabaseStubContent($file = 'migration')
    {
        return File::get($this -> module['database'][$file]['stub']);
    }

    protected function getDatabaseTemplate($file = 'migration')
    {
        return $this -> module['database'][$file]['template'];
    }

    // protected function getDatabaseName($file = 'migration')
    // {
    //     return $this -> module['database'][$file]['name'];
    // }

    protected function setFactoryName($name)
    {
        $this -> module['database']['factory']['name'] = $name;
    }

    protected function getFactoryName()
    {
        return $this -> module['database']['factory']['name'];
    }

    protected function setSeederName($name)
    {
        $this -> module['database']['seeder']['name'] = $name;
    }

    protected function getSeederName()
    {
        return $this -> module['database']['seeder']['name'];
    }

    protected function setMigrationName($name)
    {
        $this -> module['database']['migration']['name'] = date('Y_m_d_His') .'_create_'. $name .'_table';
    }

    protected function getMigrationName()
    {
        return $this -> module['database']['migration']['name'];
    }

    // -- Replaces the __MODEL_NAMESPACE template
    protected function databaseReplaceModelNamespace($file = 'migration')
    {
        return $this->replaceTemplate(
            $this -> module['database'][$file]['template'],
            $this -> getReplaceTemplate('model-namespace'),
            $this -> module['model']['namespace']
        );
    }

    // -- Replaces the __MODEL_NAME template
    protected function databaseReplaceModelName($file = 'migration')
    {
        return $this->replaceTemplate(
            $this -> module['database'][$file]['template'],
            $this -> getReplaceTemplate('model'),
            $this -> module['model']['name']
        );
    }

    // -- Replaces the __CONTROLLER_NAME template
    protected function databaseReplaceControllerName($file = 'migration')
    {
        return $this->replaceTemplate(
            $this -> module['database'][$file]['template'],
            $this -> getReplaceTemplate('controller'),
            $this -> module['controller']['name']
        );
    }

    // -- Replaces the __DB_TABLE template
    protected function databaseReplaceDatabaseTable($file = 'migration')
    {
        return $this->replaceTemplate(
            $this -> module['database'][$file]['template'],
            $this -> getReplaceTemplate('db-table'),
            $this -> module['model']['db-table']
        );
    }

    protected function getDatabaseFileName($file = 'migration')
    {
        return $this -> module['database'][$file]['name'];
    }

    protected function getDatabasePath($file = 'migration')
    {
        switch ($file) {
            case 'migration':
                return database_path('migrations');
            break;

            case 'factory':
                return database_path('factories');
            break;

            case 'seeder':
                return database_path('seeds');
            break;
        }
    }

    protected function getDatabaseFilePath($file = 'migration')
    {
        switch ($file) {
            case 'migration':
                return $this -> getDatabasePath($file) . DIRECTORY_SEPARATOR . $this -> getMigrationName() .'.php';
            break;

            case 'factory':
                return $this -> getDatabasePath($file) . DIRECTORY_SEPARATOR . $this -> getFactoryName() .'.php';
            break;

            case 'seeder':
                return $this -> getDatabasePath($file) . DIRECTORY_SEPARATOR . $this -> getSeederName() .'.php';
            break;
        }
    }

    protected function craftDatabase($file = 'migration')
    {
        $this -> putContentInFile(
            $this -> getDatabaseFilePath($file),
            $this -> getDatabaseTemplate($file)
        );
    }

    protected function uncraftDatabase()
    {
        $path = $this -> getDatabasePath('migration')
                    . DIRECTORY_SEPARATOR . '*'
                    . substr($this -> getMigrationName(), 18) . '.php';

        foreach (glob($path) as $file) {
            $this -> deleteFile($file);
        }

        $this -> deleteFile($this -> getDatabaseFilePath('seeder'));
        $this -> deleteFile($this -> getDatabaseFilePath('factory'));
    }
}
