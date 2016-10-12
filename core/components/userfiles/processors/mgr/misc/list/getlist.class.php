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

        $c = $this->modx->newQuery($class);
        $c->sortby('list', 'ASC');
        $c->select('list as name, list as id');
        $c->groupby('list');
        $c->limit(0);
        if (!empty($query)) {
            $c->where(array('list:LIKE' => "%{$query}%"));
        }
        if ($c->prepare() AND $c->stmt->execute()) {
            $array = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($array as $k => $v) {
                $array[$k]['name'] = $this->modx->lexicon($array[$k]['name']);
            }
        } else {
            $array = array();
        }

        if (!empty($query) AND empty($array)) {
            $array = array(
                'id'   => $query,
                'name' => $query
            );
        }

        return $this->outputArray($array);
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

return 'modUserFilesListGetListProcessor';