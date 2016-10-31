<?php

class modUserFilesListGetListProcessor extends modObjectProcessor
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
        $start = (int)$this->getProperty('start', 0);
        $limit = (int)$this->getProperty('limit', 10);


        $c = $this->modx->newQuery($class);
        $c->sortby('list', 'ASC');
        $c->select('list as name, list as id');
        $c->groupby('list');
        $c->limit(0);
        if (!empty($query)) {
            $c->where(array('list:LIKE' => "%{$query}%"));
        }
        if ($c->prepare() AND $c->stmt->execute()) {
            $values = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($values as $k => $v) {
                $values[$k]['name'] = $this->modx->lexicon($values[$k]['name']);
            }
        } else {
            $values = array();
        }

        if (!empty($query) AND empty($values)) {
            $values = array(
                'id'   => $query,
                'name' => $query
            );
        }

        $count = count($values);
        $values = array_slice($values, $start, $limit);

        return $this->outputArray($values, $count);
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        $array = array_merge_recursive(array(
            array(
                'id'   => $this->modx->getOption('userfiles_list_default', null, 'default', true),
                'name' => $this->modx->getOption('userfiles_list_default', null, 'default', true),
            )
        ), $array);
        if ($count) {
            $count++;
        }

        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('userfiles_all')
                )
            ), $array);
            if ($count) {
                $count++;
            }
        }

        return parent::outputArray($array, $count);
    }

}

return 'modUserFilesListGetListProcessor';