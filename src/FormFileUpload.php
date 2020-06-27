<?php

namespace Yoop;

class FormFileUpload extends FormBuilder
{
    protected $inputFileName;

    protected $targetDirectory;

    protected $requestMethod;

    protected $typeAllowed = [];


    public function __construct(string $inputFileName)
    {
        $this->inputFileName = $inputFileName;
    }

    /**
     * Permet d'ajouter des types
     */
    public function acceptedFormat(array $type)
    {
        $this->typeAllowed = array_merge($this->typeAllowed, $type);
    }

    public function control()
    {
        if(sizeof($this->typeAllowed) == 0) $this->errors[] = 'ERREUR DE GESTION DU FORMULAIRE';
        $file = $_FILES[$this->inputFileName];
        // ceci permet de sécurisé contre le directory traversal
        $file['name'] = basename($file['name']);
        // control de l'upload réussi
        if(!isset($file['error']) || $file['error'] != 0 || empty($file['name']) || empty($file['tmp_name'])) {
            $this->errors[] = 'L\'envoi du fichier a échoué.';
            // inutile d'aller plus loin dans ce cas
            return $this->errors;
        }
        // control le type de fichier
        if(empty($file['type']) || !array_key_exists(mime_content_type($file['tmp_name']), $this->typeAllowed)) {
            $this->errors[] = 'Le format du fichier est incorrect.';
        }

        // on vérifie que l'extension correspond au format
        if(!isset($this->typeAllowed[$file['type']]) || $this->typeAllowed[$file['type']] !== pathinfo($file['name'], PATHINFO_EXTENSION)) {
            $this->errors[] = 'L\'extension du fichier est incorrecte.';
        }

        return $this->errors;
    }

    public function save(string $target, ?string $filename = null)
    {
        $target = $target.($filename??basename($_FILES[$this->inputFileName]['name']));
        if(move_uploaded_file($_FILES[$this->inputFileName]["tmp_name"], $target)) {
            return str_replace($_SERVER["DOCUMENT_ROOT"], '', $target);
        }
        return '';
    }
}