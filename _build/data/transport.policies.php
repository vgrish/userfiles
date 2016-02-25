<?php

$policies = array();

$tmp = array(
    'UserFilesPolicy' => array(
        'description' => 'A policy for create / update UserFiles.',
        'data'        => array(
            'userfiles_file_list'   => true,
            'userfiles_file_upload' => true,
            'userfiles_file_update' => true,
            'userfiles_file_remove' => true,
        ),
    ),

);

foreach ($tmp as $k => $v) {
    if (isset($v['data'])) {
        $v['data'] = $modx->toJSON($v['data']);
    }
    /* @var $policy modAccessPolicy */
    $policy = $modx->newObject('modAccessPolicy');
    $policy->fromArray(array_merge(array(
            'name'    => $k,
            'lexicon' => PKG_NAME_LOWER . ':permissions',
        ), $v)
        , '', true, true);
    $policies[] = $policy;
}

return $policies;