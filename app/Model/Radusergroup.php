<?php

class Radusergroup extends AppModel
{

    var $useTable = 'radusergroup';
    var $primaryKey = 'username';
    var $primaryKeyArray = array('username', 'groupname');
    var $displayField = 'username';
    var $name = 'Radusergroup';

    public function exists($reset = false) {
        if (!empty($this->__exists) && $reset !== true) {
            return $this->__exists;
        }
        $conditions = array();
        foreach ($this->primaryKeyArray as $pk) {
            if (isset($this->data[$this->alias][$pk]) && $this->data[$this->alias][$pk]) {
                $conditions[$this->alias.'.'.$pk] = $this->data[$this->alias][$pk];
            }
            else {
                $conditions[$this->alias.'.'.$pk] = 0;
            }
        }
        $query = array('conditions' => $conditions, 'fields' => array($this->alias.'.'.$this->primaryKey), 'recursive' => -1, 'callbacks' => false);
        if (is_array($reset)) {
            $query = array_merge($query, $reset);
        }
        if ($exists = $this->find('first', $query)) {
            $this->__exists = 1;
            $this->id = $exists[$this->alias][$this->primaryKey];
            return true; 
        }
        else {
            return parent::exists($reset);
        }
    }
}

?>
