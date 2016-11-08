<?php

class Matter {
    private $action = null;
    private $args = null;

    public function __construct($listArgs) {
        $this->action = $listArgs[1];
        $this->args = array_slice($listArgs, 1, count($listArgs));
    }

    public function launch () {
        switch ($this->action) {
            case 'build:app':
                $appName = strtolower($this->args[1]);

                // Build app directory
                echo "\nApp [" . $appName . "] building...\n";
                mkdir('apps/' . $appName);

                // Declare app in app.xml
                $file = fopen('kernel/conf/apps.xml', "r+");
                fseek($file, -11, SEEK_END);
                $t = file_get_contents('TApps'); $t = str_replace('#ClassName#', $appName, $t);
                fwrite($file, $t);
                fclose($file);

                // Build controller and directory
                echo "+ Controller\n";
                mkdir('apps/' . $appName . '/controller');
                $file = fopen('apps/' . $appName . '/controller/' . $appName . 'Ctrl.php', 'a');
                $t = file_get_contents('TController'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                // Build modele and directory
                echo "+ Model\n";
                mkdir('apps/' . $appName . '/model');
                $file = fopen('apps/' . $appName . '/model/' . $appName . 'Mdl.php', 'a');
                $t = file_get_contents('TModel'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                // Build view and directory
                echo "+ View\n";
                mkdir('apps/' . $appName . '/View');
                $file = fopen('apps/' . $appName . '/View/' . $appName . 'View.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/View/Css');
                $file = fopen('apps/' . $appName . '/View/Css/' . $appName . '.css', 'a');
                $t = file_get_contents('TViewCss'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);

                // Build assets
                echo "+ Assets\n";
                mkdir('apps/' . $appName . '/assets/css');
                $file = fopen('apps/' . $appName . '/assets/css/' . $appName . '.css', 'a');
                $t = file_get_contents('TViewCss'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/assets/js');
                $file = fopen('apps/' . $appName . '/assets/js/' . $appName . '.js', 'a');
                $t = file_get_contents('TControllerJs'); $t = str_replace('#ClassName#', $appName, $t);
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/assets/i18n');
                $file = fopen('apps/' . $appName . '/assets/i18n/en.ini', 'a');
                $t = file_get_contents('TLocale_en.ini');
                fputs($file, $t);
                fclose($file);
                mkdir('apps/' . $appName . '/assets/img');
                $file = fopen('apps/' . $appName . '/assets/i18n/fr_FR.ini', 'a');
                $t = file_get_contents('TLocale_fr_FR.ini');
                fputs($file, $t);
                fclose($file);

                echo "=> Your app is done! (https://your-project/$appName)\n";
                break;
            case 'build:view':
                $appName = ucfirst($this->args[1]);
                $viewName = ucfirst($this->args[2]);
                echo "\nView [" . $viewName . "] building in App [" . $appName . "]...\n";
                $file = fopen('Apps/' . $appName . '/Vues/' . $viewName . 'Vue.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $viewName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ View\n";

                echo "=> Your view is done! Have to add it in your controller constructor for use it\n";
                break;
            case 'build:modele':
                $appName = ucfirst($this->args[1]);
                $modeleName = ucfirst($this->args[2]);
                echo "\nModele [" . $modeleName . "] building in App [" . $appName . "]...\n";
                $file = fopen('Apps/' . $appName . '/Modeles/' . $modeleName . 'Mdl.php', 'a');
                $t = file_get_contents('TView'); $t = str_replace('#ClassName#', $modeleName, $t);
                fputs($file, $t);
                fclose($file);
                echo "+ Modele\n";

                echo "=> Your modele is done! Have to add it in your controller constructor for use it\n";
                break;
        }
    }
}