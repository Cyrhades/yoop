<?php
 
 namespace Yoop;
  
 abstract class AbstractRepository
 {
    protected $entity; 

    protected function getEntityManager($classe)
    {
        $this->entity = $classe;
        return DatabaseMySql::getDb();
    }

    public function __call($method, $params = [])
    {
        if(strpos($method, 'getBy') !== false) {
            $this->getByAnythingField($method, $params);
        }
    }

    /**
     * Cette méthode permet de gérer toute les type de requetes 
     * du type getById, getByEmail, etc 
     */
    private function getByAnythingField($method, $params = [])
    {
        $property = str_replace('getBy','', $method); 
        $entityName = str_replace(['Repository','\\\\'],['','\\Entity\\'], \get_called_class()); 
        // On vérifie que la propriété existe dans l'entity
        if(property_exists($entityName, lcfirst($property))) {
            $field = strtolower($property); 
            $conn = $this->getEntityManager($entityName);
            $table = substr($entityName, strrpos($entityName, '\\')+1).'s';

            $query = $conn->prepare('SELECT * FROM `'.$table.'` WHERE `'.$field.'`=?');
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
            $query->execute($params);
            $entity = $query->fetch() ?? null;
            
            return $entity;
        }
    }
}
 