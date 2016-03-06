<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/remove.class.php';

/**
 * Remove a UserFile
 */
class modWebUserFileRemoveProcessor extends modUserFileRemoveProcessor
{
    public $classKey = 'UserFile';
    public $objectType = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_remove';

    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var Tools $Tools */
    public $Tools;


    /** {@inheritDoc} */
    public function beforeRemove()
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

        foreach (array('class', 'parent', 'list', 'source', 'anonym') as $key) {
            $this->setProperty($key, $properties[$key]);
        }

        if(!$this->getProperty('anonym', false) AND empty($this->modx->user->id)) {
            return $this->UserFiles->lexicon('err_lock');
        }

        $createdby = $this->object->get('createdby');
        $session = $this->object->get('session');

        switch (true) {
            case !empty($createdby) AND $createdby != $this->modx->user->id:
                return $this->modx->lexicon('userfiles_err_lock');
            case empty($createdby) AND $createdby == $this->modx->user->id AND $session != session_id():
                return $this->modx->lexicon('userfiles_err_lock');
        }

        return parent::beforeRemove();
    }
}

return 'modWebUserFileRemoveProcessor';