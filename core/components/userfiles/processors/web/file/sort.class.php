<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/sort.class.php';

class modWebUserFileSortProcessor extends modUserFileSortProcessor
{
    public $classKey = 'UserFile';
    public $permission = 'userfiles_file_update';

    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var Tools $Tools */
    public $Tools;

    public function process()
    {
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();
        $this->Tools = $this->UserFiles->Tools;

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->UserFiles->lexicon('err_propkey_ns');
        }

        $properties = $this->getProperty('properties', $this->UserFiles->getProperties($propKey));
        $properties = (is_string($properties) AND strpos($properties, '{') === 0)
            ? $this->modx->fromJSON($properties)
            : $properties;
        if (empty($properties)) {
            return $this->UserFiles->lexicon('err_properties_ns');
        }

        if (
            $this->UserFiles->getOption('salt', $properties, '12345678', true) !=
            $this->UserFiles->getOption('salt', null, '12345678', true)
        ) {
            return $this->UserFiles->lexicon('err_lock');
        }

        $ids = $this->getProperty('ids', array());
        if (count($ids) < 2) {
            return $this->modx->error->failure();
        }

        foreach ($ids as $idx => $id) {
            if ($file = $this->modx->getObject($this->classKey, (int)$id)) {
                $file->set('rank', $idx);
                $file->save();
            }
        }

        //$this->setProperty('source', $ids[0]);
        //$this->setProperty('target', $ids[1]);

        return parent::process();
    }

}

return 'modWebUserFileSortProcessor';
