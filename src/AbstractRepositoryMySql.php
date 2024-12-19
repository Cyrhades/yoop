<?php

namespace Yoop;

use Yoop\Database\MySql;

abstract class AbstractRepositoryMySql extends AbstractRepository
{
    protected $table;
    protected $entity;
    
    public function __construct() {
        parent::__construct('mysql');
        $this->table = $this->repositoryToTableName();       
        $this->entity = 'App\\Entity\\'.preg_replace("/App\\\Repository\\\(.*?)Repository/i", "$1", get_called_class());
        #todo : Ajouter des controles de sécurité sur le nom de la table et le nom de l'entité

        // Vérification du nom de la table avec la regex
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $this->table) || strlen($this->table) > 64) {
            throw new Error("Nom de votre table incorrecte");
        } 
    }

    public function findOneBy(array $filters = [])
    {    
        $statement = $this->db->prepare('SELECT * FROM `'.$this->table.'`'.$this->whereClause($filters).' LIMIT 1');
        $statement->execute($filters);
        // @todo vérifier si entity est une instance de Yoop\EntityInterface
        if($this->entity) {            
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
        }

        return $statement->fetch();
    }

    public function findBy(array $filters = [])
    {    
        $statement = $this->db->prepare('SELECT * FROM `'.$this->table.'`'.$this->whereClause($filters));
        $statement->execute($filters);
        // @todo vérifier si entity est une instance de Yoop\EntityInterface
        if($this->entity) {            
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
        }

        return $statement->fetchAll();
    }

    /**
     * L'utilisateur récupére PDO
     */
    public function getPDO()
    {    
        return $this->db;
    }

    /**
     * L'utilisateur crée lui même sa requete
     */
    public function query(string $query)
    {    
        $statement = $this->db->query($query);
        // @todo vérifier si entity est une instance de Yoop\EntityInterface
        if($this->entity) {            
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
        }

        return $statement->fetch();
    }

    /**
     * Préparation de la clause WHERE
     */
    private function whereClause(array $filters = []): string 
    {
        if(sizeof($filters)==0) return '';

        $conditions = [];
        foreach ($filters as $key => $value) $conditions[] = "`$key`=:$key";

        return ' WHERE '.implode(' AND ', $conditions);
    }

    /**
     * Retourne le nom de la table par rapport au repository
     */
    private function repositoryToTableName(): string  {
        return strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0',
            trim(str_replace(['App','Repository'],'',get_called_class()),'\\'))
        );
    }
}
