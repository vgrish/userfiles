<?php

if (!$object->xpdo) {
    return false;
}
/** @var modX $modx */
$modx =& $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $exists = $modx->getCount('modSystemSetting', array('key' => 'userfiles_list_max_count'));

        return !$exists;
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;