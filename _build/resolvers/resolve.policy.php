<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /* assign policy to template */
        if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'UserFilesPolicy'))) {
            if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'UserFilesPolicyTemplate'))) {
                $policy->set('template', $template->get('id'));
                $policy->save();
            } else {
                $modx->log(modX::LOG_LEVEL_INFO,
                    '[UserFiles] Could not find UserFilesPolicyTemplate Access Policy Template!');
            }
        } else {
            $modx->log(modX::LOG_LEVEL_INFO, '[UserFiles] Could not find UserFilesPolicy Access Policy!');
        }

        break;
}

return true;