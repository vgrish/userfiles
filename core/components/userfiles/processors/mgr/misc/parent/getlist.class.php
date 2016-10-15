<?php

class modResourceGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = '';
    public $classKey = '';
    public $defaultSortField = '';

    public $fieldId;
    public $fieldName;

    /** {@inheritDoc} */
    public function initialize()
    {

        $class = $this->getProperty('class');
        if (empty($class)) {
            $class = 'modResource';
        }
        $this->classKey = $this->objectType = $class;

        $this->fieldId = $this->modx->getPK($this->classKey);
        if (empty($this->fieldId)) {
            $response = array(
                'success' => true,
                'results' => array(),
                'total'   => 0
            );
            echo $this->modx->toJSON($response);
            exit;
        }

        $fields = array_keys($this->modx->getFields($this->classKey));
        foreach ($fields as $field) {
            switch ($field) {
                case 'username':
                case 'pagetitle':
                case 'name':
                    $this->fieldName = $field;
                    break;
            }
            if ($this->fieldName) {
                break;
            }
        }

        if (empty($this->fieldName)) {
            $this->fieldName = $this->fieldId;
        }
        $this->defaultSortField = $this->fieldId;

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($this->getProperty('combo')) {
            $c->select("{$this->fieldId},{$this->fieldName} as name");
        }

        $query = $this->getProperty('query');
        if (!empty($query)) {
            if (is_numeric($query)) {
                $c->andCondition(array(
                    'id' => $query,
                ));
            } else {
                $c->where(array(
                    "{$this->fieldName}:LIKE" => "%{$query}%"
                ));
            }

        }

        return $c;
    }

    /** {@inheritDoc} */
    public function prepareRow(xPDOObject $object)
    {
        if ($this->getProperty('combo')) {
            $array = array(
                'id'   => $object->get($this->fieldId),
                'name' => $object->get($this->fieldName)
            );
        } else {
            $array = $object->toArray();
        }

        return $array;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('userfiles_all')
                )
            ), $array);
        }

        return parent::outputArray($array, $count);
    }


}

return 'modResourceGetListProcessor';