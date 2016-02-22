<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersMainManagerController extends UserFilesMainController
{

    public static function getDefaultController()
    {
        return 'main';
    }

}

class UserFilesMainManagerController extends UserFilesMainController
{

    public function getPageTitle()
    {
        return $this->modx->lexicon('userfiles') . ' :: ' . $this->modx->lexicon('userfiles_main');
    }

    public function getLanguageTopics()
    {
        return array('userfiles:default');
    }

    public function loadCustomCssJs()
    {

        $this->UserFiles->Tools->addFilesController($this, array(
            'main'     => true,
            'file'     => true,
            'jquery'   => true,
            'dropzone' => true,
            'cropper'  => true
        ));

        $script = 'Ext.onReady(function() {
			MODx.add({ xtype: "userfiles-panel-main"});
		});';
        $this->addHtml("<script type='text/javascript'>{$script}</script>");

        $this->modx->invokeEvent('UserFilesOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'main'));

    }

    public function getTemplateFile()
    {
        return $this->UserFiles->config['templatesPath'] . 'main.tpl';
    }

}

// MODX 2.3
class ControllersMgrMainManagerController extends ControllersMainManagerController
{

    public static function getDefaultController()
    {
        return 'main';
    }

}

class UserFilesMgrMainManagerController extends UserFilesMainManagerController
{

}
