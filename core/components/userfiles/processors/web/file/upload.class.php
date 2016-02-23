<?php

class modUserFileUploadProcessor extends modObjectCreateProcessor
{
    public $classKey = 'UserFile';
    public $objectType = 'UserFile';
    public $primaryKeyField = 'id';
    public $languageTopics = array('userfiles');
    public $permission = '';

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
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        $this->modx->log(1, print_r('modUserFileUploadProcessor' ,1));
        $this->modx->log(1, print_r($this->getProperties() ,1));

        return true;
    }

    public function process() {

        return true;
    }

}

return 'modUserFileUploadProcessor';
