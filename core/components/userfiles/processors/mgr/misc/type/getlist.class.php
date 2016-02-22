<?php

class modUserFilesTypeGetListProcessor extends modObjectProcessor
{
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');

    public function process()
    {
        $class = $this->getProperty('class', $this->classKey);
        if (empty($class)) {
            $class = $this->classKey;
        }

        $query = $this->getProperty('query');

        $c = $this->modx->newQuery($class);
        $c->sortby('type', 'ASC');
        $c->select('type as name, type as id');
        $c->groupby('type');
        $c->limit(0);

        $c->where(array("{$this->classKey}.class:!=" => $this->classKey));

        if (!empty($query)) {
            $c->where(array('type:LIKE' => "%{$query}%"));
        }
        if ($c->prepare() && $c->stmt->execute()) {
            $array = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($array as $k => $v) {
                $array[$k]['name'] = $this->modx->lexicon($array[$k]['name']);
            }

        } else {
            $array = array();
        }

        return $this->outputArray($array);
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

return 'modUserFilesTypeGetListProcessor';