<?php

/**
 * Update an UserFile
 */
class modUserFileUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'UserFile';
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_update';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('payandsee_err_ns');
        }

        return parent::beforeSet();
    }

    /**
     * @return bool
     */
    public function afterSave()
    {
        $children = $this->object->getMany('Children');
        /* @var UserFile $child */
        foreach ($children as $child) {
            $child->fromArray(array(
                'name'        => $this->object->get('name'),
                'description' => $this->object->get('description')
            ));
            $child->save();
        }

        return parent::beforeSave();
    }

}

return 'modUserFileUpdateProcessor';
