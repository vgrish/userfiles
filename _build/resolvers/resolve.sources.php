<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $tmp = explode('/', MODX_ASSETS_URL);
        $assets = $tmp[count($tmp) - 2];
        $properties = array(
            'name'        => 'User Files',
            'description' => 'Default media source for files of UserFiles',
            'class_key'   => 'sources.modFileMediaSource',
            'properties'  => array(
                'basePath'         => array(
                    'name'    => 'basePath',
                    'desc'    => 'prop_file.basePath_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => $assets . '/userfiles/',
                ),
                'baseUrl'          => array(
                    'name'    => 'baseUrl',
                    'desc'    => 'prop_file.baseUrl_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => 'assets/userfiles/',
                ),
                'imageExtensions'  => array(
                    'name'    => 'imageExtensions',
                    'desc'    => 'prop_file.imageExtensions_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => 'jpg,jpeg,png,gif',
                ),
                'allowedFileTypes' => array(
                    'name'    => 'allowedFileTypes',
                    'desc'    => 'prop_file.allowedFileTypes_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'core:source',
                    'value'   => 'jpg,jpeg,png,gif,doc,pdf,txt',
                ),
                'thumbnailType'    => array(
                    'name'    => 'thumbnailType',
                    'desc'    => 'prop_file.thumbnailType_desc',
                    'type'    => 'list',
                    'lexicon' => 'core:source',
                    'options' => array(
                        array('text' => 'png', 'value' => 'png'),
                        array('text' => 'jpg', 'value' => 'jpg')
                    ),
                    'value'   => 'jpg',
                ),
                'imageThumbnails'  => array(
                    'name'    => 'imageThumbnails',
                    'desc'    => 'userfiles_source_thumbnail_desc',
                    'type'    => 'textarea',
                    'lexicon' => 'userfiles:setting',
                    'value'   => '[{"w":120,"h":90,"q":90,"zc":"1","bg":"fff"}]',
                ),
                'maxUploadSize'    => array(
                    'name'    => 'maxUploadSize',
                    'desc'    => 'userfiles_source_maxUploadSize_desc',
                    'type'    => 'numberfield',
                    'lexicon' => 'userfiles:setting',
                    'value'   => 3145728,
                ),
                'imageNameType'    => array(
                    'name'    => 'imageNameType',
                    'desc'    => 'userfiles_source_imageNameType_desc',
                    'type'    => 'list',
                    'lexicon' => 'userfiles:setting',
                    'options' => array(
                        array('text' => 'hash', 'value' => 'hash'),
                        array('text' => 'friendly', 'value' => 'friendly'),
                    ),
                    'value'   => 'hash',
                ),
                'thumbnailName'    => array(
                    'name'    => 'thumbnailName',
                    'desc'    => 'userfiles_source_thumbnail_name_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'userfiles:setting',
                    'value'   => '{name}.{rand}.{w}.{h}.{ext}',
                ),
                'fileName'    => array(
                    'name'    => 'fileName',
                    'desc'    => 'userfiles_source_file_name_desc',
                    'type'    => 'textfield',
                    'lexicon' => 'userfiles:setting',
                    'value'   => '{name}.{rand}.{ext}',
                ),
            )
            ,
            'is_stream'   => 1
        );
        /* @var $source modMediaSource */
        if (!$source = $modx->getObject('sources.modMediaSource', array('name' => $properties['name']))) {
            $source = $modx->newObject('sources.modMediaSource', $properties);
        } else {
            $default = $source->get('properties');
            foreach ($properties['properties'] as $k => $v) {
                if (!array_key_exists($k, $default)) {
                    $default[$k] = $v;
                }
            }
            $source->set('properties', $default);
        }
        $source->save();
        if ($setting = $modx->getObject('modSystemSetting', array('key' => 'userfiles_source_default'))) {
            if (!$setting->get('value')) {
                $setting->set('value', $source->get('id'));
                $setting->save();
            }
        }
        @mkdir(MODX_ASSETS_PATH . 'userfiles/');
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return true;