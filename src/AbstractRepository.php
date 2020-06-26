<?php
 
namespace Yoop;
  
abstract class AbstractRepository
{
    protected $entity; 

    protected function getEntityManager(?string $classe = null)
    {
        if($classe === null) {
            $this->entity =  str_replace(['Repository','\\\\'],['','\\Entity\\'], \get_called_class()); 
        } else {

            $this->entity = $classe;
        }
        return DatabaseMySql::getDb();
    }

    public function __call(string $method, array $params = [])
    {
        if(strpos($method, 'getBy') !== false || strpos($method, 'findBy') !== false) {
           return $this->findByAnythingField($method, $params);
        } elseif(strpos($method, 'getOneBy') !== false || strpos($method, 'findOneBy') !== false) {
           return $this->findByAnythingField($method, $params, true);
        } elseif(strpos($method, 'get') !== false || strpos($method, 'find') !== false) {
            return $this->findAnything($method, ...$params);
        }
    }

    /**
     * Cette méthode permet de gérer toute les type de requetes 
     * du type getById, getByEmail, etc 
     */
    private function findByAnythingField($method, $params = [], $onlyOne = false)
    {
        $property = str_replace(['findBy','getBy','findOneBy','getOneBy'],'', $method); 
        $conn = $this->getEntityManager();

        // On vérifie que la propriété existe dans l'entity
        if(property_exists($this->entity, lcfirst($property))) {
            
            $field = lcfirst($property); 
            $table = substr($this->entity, strrpos($this->entity, '\\')+1).'s';

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


    /**
     * Cette méthode permet de gérer toute les type de requetes find
     */
    private function findAnything(string $method, string $where = '', array $params = [], int $limit = 0, int $offset = 0)
    {
        $property = str_replace(['find','get'],'', $method); 
        $conn = $this->getEntityManager();

        $table = substr($this->entity, strrpos($this->entity, '\\')+1).'s';
        $where = $this->constructWhere($where, $limit, $offset);
    
        $query = $conn->prepare('SELECT * FROM `'.$table.'` '.$where);
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        $query->execute($params);
     
        return $query->fetchAll() ?? null;
    }


    private function constructWhere(string $where = '', int $limit = 0, int $offset = 0)
    {
        $where = (!empty($where)) ? ' WHERE '.$where : '';
      
        if($limit > 0) {
            $where .= ' LIMIT '.intval($limit);
        }
        if($offset > 0) {
            $where .= ' OFFSET '.intval($offset);
        }
        return $where;
    }
}