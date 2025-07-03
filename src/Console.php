<?php

namespace Yoop;

class Console {

    public function execute(string $action, ...$args) {
        $action = $this->toPascalCase($action);

        if(!empty($action) && is_string($action)) {
            if(method_exists($this, $action)) {
                return call_user_func_array([$this,  $action], $args);
            }
            else {
                return "\033[31m[ERROR] \033[0mLa commande \"$action\" n'est pas reconnue par la console Yoop.";
            }
        }
        elseif(empty($action) || trim($action) === "") {
            return 'Pour quitter la console tapez "quit"';
        }
        else {
            return "\033[31m[ERROR] \033Une erreur inconnue a eu lieu.";
        }
    }

    /**
     * Création du controller et du template via la console (cmd : make:controller <controller_name>)
     */
    private function makeController($nameController = null) {        
        if($nameController === null) {
            return $this->messageShell("error", "Vous devez préciser un nom pour votre controller");
        }         
        //-------------------
        // Controller
        //-------------------
        $directoryController =  dirname(__DIR__,4).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR;
        $nameController = str_replace('Controller','', ucfirst($nameController));
        $fullNameController = $nameController.'Controller';
        //-------------------
        // Template
        //-------------------
        $directoryTemplate =  dirname(__DIR__,4).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        $nameTemplate =  $this->pascalCaseToSnakeCase($nameController);
        $NameTemplateWithoutExtension = $nameTemplate.DIRECTORY_SEPARATOR.'index';
        $fullNameTemplate = $nameTemplate.DIRECTORY_SEPARATOR.'index.html.twig';
        // Proposition de route
        $route = "\$router->addRoute('GET', '/".$nameTemplate."', 'App\Controller\\".$fullNameController."::index');";

        //-------------------
        // Verification
        //-------------------
        // Si le nom du controller n'est pas correct
        if (!preg_match('/^[A-Z][a-zA-Z0-9_]*$/', $fullNameController)) {
            return $this->messageShell("error", 'Votre controller doit avoir un nom correct.');
        }
        // Si le controller existe déjà
        if(file_exists($directoryController.$fullNameController.'.php')) {
            return $this->messageShell("error", "Le controller \"$fullNameController\" existe déjà dans votre application.");
        }

        // Si le dossier du template existe déjà
        if(file_exists($directoryTemplate.$nameTemplate)) {
            return $this->messageShell("error", "Le répertoire du template \"$nameTemplate\" existe déjà dans votre application.");
        }

        // On crée le controller
        $contentController = base64_decode('PD9waHANCm5hbWVzcGFjZSBBcHBcQ29udHJvbGxlcjsNCg0KdXNlIFlvb3BcQWJzdHJhY3RDb250cm9sbGVyOw0KDQpjbGFzcyB7e05BTUVfQ09OVFJPTExFUn19IGV4dGVuZHMgQWJzdHJhY3RDb250cm9sbGVyDQp7DQogICAgcHVibGljIGZ1bmN0aW9uIGluZGV4KCkgDQogICAgew0KICAgICAgICByZXR1cm4gJHRoaXMtPnJlbmRlcigne3tOQU1FX1RFTVBMQVRFfX0nLA0KCQkgICAgWydjb250cm9sbGVyX25hbWUnID0+ICd7e05BTUVfQ09OVFJPTExFUn19J10NCiAgICAgICAgKTsNCiAgICB9DQp9');
        file_put_contents($directoryController.$fullNameController.'.php', 
            str_replace(['{{NAME_CONTROLLER}}','{{NAME_TEMPLATE}}'],[$fullNameController, $NameTemplateWithoutExtension], $contentController)
        );

        // On crée le répertoire où le template sera stocké
        mkdir($directoryTemplate.$nameTemplate);
        // On crée le template
        $contentTemplate = base64_decode('eyMgLi90ZW1wbGF0ZXMve3tGVUxMX05BTUVfVEVNUExBVEV9fSAjfQ0KeyUgZXh0ZW5kcyAnd2ViL2xheW91dC5odG1sLnR3aWcnICV9DQoNCnslIGJsb2NrIGNvbnRlbnQgJX0NCjxkaXYgY2xhc3M9ImNvbnRhaW5lciI+DQogICA8cD4NCiAgICAgICBWb3VzIGRldmV6IG1vZGlmaWVyIGxlIGNvbnRyb2xsZXIgZXQgbGUgdGVtcGxhdGUgZW4gZm9uY3Rpb24gZGUgdm9zIGJlc29pbnMgIQ0KICAgICAgIDxiciAvPjxiciAvPg0KICAgICAgIDxzdHJvbmc+RmljaGllciBDb250cm9sbGVyIDo8L3N0cm9uZz4gLi9zcmMvQ29udHJvbGxlci97e0ZVTExfTkFNRV9DT05UUk9MTEVSfX08YnIgLz4NCiAgICAgICA8c3Ryb25nPkZpY2hpZXIgdGVtcGxhdGUgOjwvc3Ryb25nPiAuL3RlbXBsYXRlcy97e0ZVTExfTkFNRV9URU1QTEFURX19DQogICA8L3A+DQo8L2Rpdj4NCnslIGVuZGJsb2NrICV9');
        file_put_contents($directoryTemplate.$fullNameTemplate, 
            str_replace(
                ['{{FULL_NAME_CONTROLLER}}','{{FULL_NAME_TEMPLATE}}'],
                [str_replace(DIRECTORY_SEPARATOR,'/',$fullNameController), str_replace(DIRECTORY_SEPARATOR,'/',$fullNameTemplate)], 
                $contentTemplate
            )
        );

        // Détails de ce qui a été créée
        return  $this->messageShell("success", "Création du Controller et du template de base\n\n").
                $this->messageShell("info", "Pensez à ajouter la route dans votre fichier ./app/routes.php\n".$route);
    }

    /**
     * help, affichage de l'aide
     */
    private function help($input = null)
    {
        if($input === null) {
            return $this->messageShell(
                "info","Actuellement la console ne permet que de créer un controller et le template associé.
                \n\r> make:controller <controller_name>  - Permet de créer un controller et le template associé"
            );
        } else {
            // On vérifie ce qu'il y a derriere pour afficher l'aide d'uniquement ce qui est demandé.
            return $this->messageShell(
                "info", 
                "Aucune aide n'est disponible pour votre demande"
            );
        }
    }

    private function toPascalCase($input)
    {
        return lcfirst(str_replace(' ', '',  ucwords(preg_replace('/[^a-zA-Z]/', ' ', $input))));
    }

    private function pascalCaseToSnakeCase($input)
    {
       return trim(preg_replace_callback('/[A-Z]/', function ($match) { return '_'.strtolower($match[0]);}, $input), '_');
    }

    private function messageShell(string $type, string $msg) {
        switch($type) {
            case 'info' : return "\033[34m [INFO] \033[0m".$msg; break;
            case 'warning' : return "\033[33m [WARNING] \033[0m".$msg; break;
            case 'success' : return "\033[32m [SUCCESS] \033[0m".$msg; break;
            case 'error' : return "\033[31m [ERROR] \033[0m".$msg; break;
        }
        return $msg;
    }
}