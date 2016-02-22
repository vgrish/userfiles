<?php

abstract class UserFilesPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var UserFiles $UserFiles */
    protected $UserFiles;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->UserFiles = $this->modx->UserFiles;

        if (!is_object($this->UserFiles) OR !($this->UserFiles instanceof UserFiles)) {
            $corePath = $this->modx->getOption('userfiles_core_path', null,
                $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
            $this->UserFiles = $this->modx->getService('UserFiles', 'UserFiles', $corePath . 'model/userfiles/',
                $this->scriptProperties);
        }

        if (!$this->UserFiles->initialized[$this->modx->context->key]) {
            $this->UserFiles->initialize($this->modx->context->key);
        }
    }

    abstract public function run();
}