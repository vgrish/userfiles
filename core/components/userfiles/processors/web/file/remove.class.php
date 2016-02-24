<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/remove.class.php';

/**
 * Remove a UserFile
 */
class modWebUserFileRemoveProcessor extends modUserFileRemoveProcessor
{
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = '';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeRemove()
    {
        return parent::beforeRemove();
    }
}

return 'modWebUserFileRemoveProcessor';