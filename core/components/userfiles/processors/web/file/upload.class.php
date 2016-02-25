<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/upload.class.php';

class modWebUserFileUploadProcessor extends modUserFileUploadProcessor
{
    public $classKey = 'UserFile';
    public $objectType = 'UserFile';
    public $primaryKeyField = 'id';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_upload';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var Tools $Tools */
    public $Tools;

    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    /** @var array $mediaSourceProperties */
    public $mediaSourceProperties;
    /** @var null $data */
    protected $data = null;

    public function initialize()
    {
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();
        $this->Tools = $this->UserFiles->Tools;

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->UserFiles->lexicon('err_propkey_ns');
        }

        $properties = $this->UserFiles->getProperties($propKey);
        if (empty($properties)) {
            return $this->UserFiles->lexicon('err_properties_ns');
        }

        //$this->modx->log(1, print_r($properties ,1));

        foreach (array('class', 'parent', 'list', 'source', 'anonym') as $key) {
            $this->setProperty($key, $properties[$key]);
        }

        if(!$this->getProperty('anonym', false) AND empty($this->modx->user->id)) {
            return $this->UserFiles->lexicon('err_lock');
        }

        return parent::initialize();
    }

}

return 'modWebUserFileUploadProcessor';
