<?php

namespace Yoop;

class Console
{
    public function execute(...$request)
    {
        if(isset($request[0])) {
            if($request[0] == 'make' && isset($request[1])) {
               
                if($request[1] == 'database') {
                   $this->makeDatabase();
                }
            }
        }
    }

    private function makeDatabase()
    {
        $entities = glob(dirname(__DIR__, 4).'/src/Entity/*.php');
        foreach($entities as $entity) {
            $fields = [];
            $index = '';
            $table = strtolower(pathinfo($entity)['filename']).'s';
            $entityName = 'App\\Entity\\'.pathinfo($entity)['filename'];

            $rc = new \ReflectionClass($entityName); 
            preg_match('/.*@mysql(.*)\s*/u', $rc->getDocComment(), $comment);
            if(!empty($comment[1])) {
                $index = trim($comment[1]);
            }
            $props = $rc->getProperties();
            foreach ($props as $prop) {
                preg_match('/.*@mysql(.*)\s*/u', $prop->getDocComment(), $comment);
                if(!empty($comment[1])) {
                    $fields[$prop->getName()] = trim($comment[1]);
                }
            }
            $sql = $this->createRequestSQLForCreateTable($table, $fields, $index);
            $db = DatabaseMySql::getDb();
    
            if(!empty($sql)) {
                $db->exec($sql);
                echo "Création de la table \"$table\"\n";
            }
        }
    }
        

    private function createRequestSQLForCreateTable(string $table, array $fields, string $index = '')
    {
        if(!empty($table) && sizeof($fields) > 0) {
            $tableSql = "CREATE TABLE IF NOT EXISTS `$table` (%s %s)";
            $fieldsSql = '';
            foreach($fields as $fieldName => $field) {
                $fieldsSql .=  '`'.$fieldName.'` '.$field.',';
            }
            if(!empty($tableSql) && !empty($fieldsSql) && !empty($index)) {
                return trim(sprintf($tableSql, $fieldsSql, $index));
            }
        }
        return '';
    }
}