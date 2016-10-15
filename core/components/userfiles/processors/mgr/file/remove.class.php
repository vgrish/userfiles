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

    /** @var UserFile $object */
    public $object;

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }

        return parent::initialize();
    }

    public function process()
    {
        $canRemove = $this->beforeRemove();
        if ($canRemove !== true) {
            return $this->failure($canRemove);
        }
        $preventRemoval = $this->fireBeforeRemoveEvent();
        if (!empty($preventRemoval)) {
            return $this->failure($preventRemoval);
        }

        if ($this->removeObject() == false) {
            return $this->failure($this->modx->lexicon($this->objectType . '_err_remove'));
        }
        $this->afterRemove();
        $this->fireAfterRemoveEvent();
        $this->logManagerAction();

        return $this->cleanup();
    }


    public function cleanup()
    {
        $array = $this->object->toArray();
        $array['product_thumb'] = $this->object->updateRanks();

        return $this->success('', $array);
    }
}

return 'modUserFileRemoveProcessor';