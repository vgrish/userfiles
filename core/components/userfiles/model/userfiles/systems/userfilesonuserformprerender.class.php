<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

class UserFilesOnUserFormPrerender extends UserFilesPlugin
{
    public function run()
    {
        $id = $this->modx->getOption('id', $this->scriptProperties, 0, true);
        $mode = $this->modx->getOption('mode', $this->scriptProperties, modSystemEvent::MODE_NEW, true);
        if ($mode == modSystemEvent::MODE_NEW OR empty($id)) {
            return;
        }

        $controller = &$this->modx->controller;
        $this->UserFiles->Tools->loadControllerJsCss($controller, array(
            'css'         => true,
            'config'      => true,
            'tools'       => true,
            'jquery'      => true,
            'dropzone'    => true,
            'cropper'     => true,
            'file'        => true,
            'inject/user' => true,
        ));

        $this->modx->invokeEvent('UserFilesOnManagerCustomCssJs',
            array('controller' => $controller, 'page' => 'user'));

    }

}