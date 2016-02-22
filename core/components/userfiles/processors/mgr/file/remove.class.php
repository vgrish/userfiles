<?php

/**
 * Remove a UserFile
 */
class modUserFileRemoveProcessor extends modObjectRemoveProcessor
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

return 'modUserFileRemoveProcessor';