<?php

/**
 * Get an UserFile
 */
class modWebUserFileGetProcessor extends modObjectGetProcessor
{

    public $objectType = 'UserFile';
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_list';

    /** {@inheritDoc} */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        return parent::process();
    }

    public function cleanup()
    {
        $row = $this->object->toArray();
        $row['dyn_url'] = $row['url'] . '?t=' . $row['size'];

        return $this->success('', $row);
    }

}

return 'modWebUserFileGetProcessor';