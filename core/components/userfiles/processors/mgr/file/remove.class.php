<?php

/**
 * Remove a UserFile
 */
class modUserFileRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'UserFile';
    public $objectType = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_remove';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeRemove()
    {
        return parent::beforeRemove();
    }
}

return 'modUserFileRemoveProcessor';