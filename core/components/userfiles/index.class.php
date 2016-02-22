<?php

/**
 * Class UserFilesMainController
 */
abstract class UserFilesMainController extends modExtraManagerController
{
    /** @var UserFiles $UserFiles */
    public $UserFiles;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('userfiles_core_path', null,
            $this->modx->getOption('core_path') . 'components/userfiles/');
        require_once $corePath . 'model/userfiles/userfiles.class.php';

        $this->UserFiles = new UserFiles($this->modx);
        $this->UserFiles->initialize($this->modx->context->key);

        $this->UserFiles->Tools->addFilesController($this, array(
            'css'    => true,
            'config' => true,
            'tools'  => true,
        ));

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('userfiles:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends UserFilesMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'main';
    }
}