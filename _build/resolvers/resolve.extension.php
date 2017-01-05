<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx->addExtensionPackage('userfiles', '[[++core_path]]components/userfiles/model/');
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->removeExtensionPackage('userfiles');
        break;
}
return true;