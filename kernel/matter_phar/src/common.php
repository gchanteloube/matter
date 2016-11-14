<?php

class Matter {
    private $action = null;
    private $args = null;

    public function __construct($listArgs) {
        $this->action = $listArgs[1];
        $this->args = array_slice($listArgs, 1, count($listArgs));
    }

    public function launch () {
        echo "--------------------------------------------------------------------\n                         Lazy matter - V2.1\n--------------------------------------------------------------------\n";

        switch ($this->action) {
            case 'build:app':
                $appName = lcfirst($this->args[1]);

                // Build app directory
                echo "\nApp [" . $appName . "] building...\n";
                mkdir('apps/' . $appName);

                // Declare app in app.xml
                $file = fopen('struct/apps.xml', "r+");
                fseek($file, -11, SEEK_END);
                $t = file_get_contents('TApps');
                $t = str_replace('#AppName#', $appName, $t);
                $t = str_replace('#ClassName#', ucfirst($appName), $t);
                fwrite($file, $t);
                fclose($file);

                // Build controller and directory
                echo "+ Controller\n";
                mkdir('apps/' . $appName . '/controller');
                $file = fopen('apps/' . $appName . '/controller/' . ucfirst($appName) . 'Ctrl.php', 'a');
                $t = file_get_contents('TController');
                $t = str_replace('#ClassName#', ucfirst($appName), $t);
                fputs($file, $t);
                fclose($file);

                // Build modele and directory
                echo "+ Model\n";
                mkdir('apps/' . $appName . '/model');
                $file = fopen('apps/' . $appName . '/model/' . ucfirst($appName) . 'Mdl.php', 'a');
                $t = file_get_contents('TModel');
                $t = str_replace('#ClassName#', ucfirst($appName), $t);
                fputs($file, $t);
                fclose($file);

                // Build view and directory
                echo "+ View\n";
                mkdir('apps/' . $appName . '/view');
                $file = fopen('apps/' . $appName . '/view/' . ucfirst($appName) . 'View.php', 'a');
                $t = file_get_contents('TView');
                $t = str_replace('#ClassName#', ucfirst($appName), $t);
                fputs($file, $t);
                fclose($file);

                // Build assets
                echo "+ Assets\n";
                mkdir('apps/' . $appName . '/assets');
                mkdir('apps/' . $appName . '/assets/img');
                mkdir('apps/' . $appName . '/assets/css');
                $file = fopen('apps/' . $appName . '/assets/css/' . $appName . '.css', 'a');
                $t = file_get_contents('TCss');
                $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/assets/js');
                $file = fopen('apps/' . $appName . '/assets/js/' . $appName . '.js', 'a');
                $t = file_get_contents('TJs');
                $t = str_replace('#ClassName#', ucfirst($appName), $t);
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/assets/i18n');
                $file = fopen('apps/' . $appName . '/assets/i18n/en.ini', 'a');
                $t = file_get_contents('TLocale_en');
                fputs($file, $t);
                fclose($file);
                $file = fopen('apps/' . $appName . '/assets/i18n/fr_FR.ini', 'a');
                $t = file_get_contents('TLocale_fr_FR');
                fputs($file, $t);
                fclose($file);

                echo "=> Your app is done! (https://your-project/$appName)\n";
                break;
            case 'build:view':
                $appName = lcfirst($this->args[1]);
                $viewName = ucfirst($this->args[2]);
                echo "\nView [" . $viewName . "] building in App [" . $appName . "]...\n";
                $file = fopen('apps/' . $appName . '/view/' . $viewName . '.php', 'a');
                $t = file_get_contents('TView');
                $t = str_replace('#ClassName#', $viewName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ View\n";

                echo "=> Your view is done!\n";
                break;
            case 'build:model':
                $appName = lcfirst($this->args[1]);
                $modelName = ucfirst($this->args[2]);
                echo "\nModel [" . $modelName . "] building in App [" . $appName . "]...\n";
                $file = fopen('apps/' . $appName . '/model/' . $modelName . '.php', 'a');
                $t = file_get_contents('TModel');
                $t = str_replace('#ClassName#', $modelName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ Model\n";

                echo "=> Your model is done!\n";
                break;
            case 'build:controller':
                $appName = lcfirst($this->args[1]);
                $controllerName = ucfirst($this->args[2]);
                echo "\nController [" . $controllerName . "] building in App [" . $appName . "]...\n";
                $file = fopen('apps/' . $appName . '/controller/' . $controllerName . '.php', 'a');
                $t = file_get_contents('TController');
                $t = str_replace('#ClassName#', $controllerName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ Controller\n";

                echo "=> Your controller is done!\n";
                break;
        }

        echo "\n";
    }
}