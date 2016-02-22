<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modelPath = $modx->getOption('userfiles_core_path', null,
                $modx->getOption('core_path') . 'components/userfiles/') . 'model/';
        $modx->addPackage('userfiles', $modelPath);

        $manager = $modx->getManager();
        $objects = array(
            'UserFile',
        );
        foreach ($objects as $tmp) {
            $manager->createObjectContainer($tmp);
        }

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;
