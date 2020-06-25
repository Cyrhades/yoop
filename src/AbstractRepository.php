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
        if(strpos($method, 'getBy') !== false || strpos($method, 'findBy') !== false) {
           return $this->findByAnythingField($method, $params);
        } elseif(strpos($method, 'getOneBy') !== false || strpos($method, 'findOneBy') !== false) {
           return $this->findByAnythingField($method, $params, true);
        }
    }

    /**
     * Cette méthode permet de gérer toute les type de requetes 
     * du type getById, getByEmail, etc 
     */
    private function findByAnythingField($method, $params = [], $onlyOne = false)
    {
        $property = str_replace(['findBy','getBy','findOneBy','getOneBy'],'', $method); 
        $entityName = str_replace(['Repository','\\\\'],['','\\Entity\\'], \get_called_class()); 

        // On vérifie que la propriété existe dans l'entity
        if(property_exists($entityName, lcfirst($property))) {
            
            $field = strtolower($property); 
            $conn = $this->getEntityManager($entityName);
            $table = substr($entityName, strrpos($entityName, '\\')+1).'s';

            $query = $conn->prepare('SELECT * FROM `'.$table.'` WHERE `'.$field.'`=?');
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
            $query->execute($params);
            
            if($onlyOne) {
                return $query->fetch() ?? null;
            }
            else {
                return $query->fetchAll() ?? null;
            }
        }
    }
}