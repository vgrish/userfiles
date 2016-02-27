<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/sort.class.php';

class modWebUserFileSortProcessor extends modUserFileSortProcessor
{
    public $classKey = 'UserFile';
    public $permission = 'userfiles_file_update';

    public function process()
    {

        $ids = $this->getProperty('ids', array());
        if (count($ids) < 2) {
            return $this->modx->error->failure();
        }

        $this->setProperty('source', $ids[0]);
        $this->setProperty('target', $ids[1]);

        return parent::process();
    }

}

return 'modWebUserFileSortProcessor';
