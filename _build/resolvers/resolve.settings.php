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

        $tpls = array(
            'userfiles_chunk_link_file'  => 'uf.link.file',
            'userfiles_chunk_link_image' => 'uf.link.image'
        );

        foreach ($tpls as $key => $tpl) {
            if ($setting = $modx->getObject('modSystemSetting', array('key' => $key))) {
                $value = $setting->get('value');
                if (empty($value) AND $chunk = $modx->getObject('modChunk', array('name' => $tpl))) {
                    $setting->set('value', $chunk->get('id'));
                    $setting->save();
                }
            }
        }

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;