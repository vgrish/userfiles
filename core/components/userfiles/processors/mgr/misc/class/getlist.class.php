<?php

class modUserFilesClassGetListProcessor extends modObjectProcessor
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
        $c->sortby('class', 'ASC');
        $c->select('class as name, class as id');
        $c->groupby('class');
        $c->limit(0);
        $c->where(array('class:!=' => $this->classKey));
        if (!empty($query)) {
            $c->where(array('class:LIKE' => "%{$query}%"));
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
            $namespace = null;
            if (in_array($query, array_keys($this->modx->map))) {
                $namespace = $query;
            } else {
                $namespace = $query;
                $corePath = $this->modx->getOption("{$namespace}.core_path", null, null, true);
                /* @var modNamespace $ns */
                if (!$corePath AND $ns = $this->modx->getObject('modNamespace', $namespace)) {
                    $corePath = $ns->getCorePath();
                }
                if (!$corePath OR !$this->modx->addPackage($namespace, $corePath . "model/")) {
                    $namespace = null;
                }
            }

            if ($namespace) {
                $array = array(
                    'id'   => $namespace,
                    'name' => $namespace
                );
            }
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

return 'modUserFilesClassGetListProcessor';