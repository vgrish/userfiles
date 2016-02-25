<?php

$templates = array();

$tmp = array(
    'UserFilesPolicyTemplate' => array(
        'description'    => 'A policy for users to create / update UserFiles.',
        'template_group' => 1,
        'permissions'    => array(
            'userfiles_file_list'   => array(),
            'userfiles_file_upload' => array(),
            'userfiles_file_update' => array(),
            'userfiles_file_remove' => array(),
        ),
    ),

);

foreach ($tmp as $k => $v) {
    $permissions = array();
    if (isset($v['permissions']) && is_array($v['permissions'])) {
        foreach ($v['permissions'] as $k2 => $v2) {
            /* @var modAccessPermission $event */
            $permission = $modx->newObject('modAccessPermission');
            $permission->fromArray(array_merge(array(
                    'name'        => $k2,
                    'description' => $k2,
                    'value'       => true,
                ), $v2)
                , '', true, true);
            $permissions[] = $permission;
        }
    }
    /* @var $template modAccessPolicyTemplate */
    $template = $modx->newObject('modAccessPolicyTemplate');
    $template->fromArray(array_merge(array(
            'name'    => $k,
            'lexicon' => PKG_NAME_LOWER . ':permissions'
        ), $v)
        , '', true, true);
    if (!empty($permissions)) {
        $template->addMany($permissions);
    }
    $templates[] = $template;
}

return $templates;