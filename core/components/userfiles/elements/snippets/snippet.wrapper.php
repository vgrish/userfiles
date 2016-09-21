<?php

/** @var array $scriptProperties */
/** @var userfiles $userfiles */
$fqn = $modx->getOption('userfiles_class', null, 'userfiles.userfiles', true);
$path = $modx->getOption('userfiles_class_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
if (!$userfiles = $modx->getService($fqn, '', $path . 'model/',
    array('core_path' => $path))
) {
    return false;
}

$tpl = $scriptProperties['tpl'] = $modx->getOption('tpl', $scriptProperties, '', true);
$parents = $scriptProperties['parents'] = $modx->getOption('parents', $scriptProperties, $modx->resource->id, true);
$class = $scriptProperties['class'] = $modx->getOption('class', $scriptProperties, 'modResource', true);
$return = $scriptProperties['return'] = $modx->getOption('return', $scriptProperties, 'chunks', true);
$includeThumbs = $scriptProperties['includeThumbs'] = $modx->getOption('includeThumbs', $scriptProperties, 0, true);
$includeThumbs = $userfiles->explodeAndClean($includeThumbs);

$element = $scriptProperties['element'] = $modx->getOption('element', $scriptProperties, 'pdoResources', true);
if (isset($this) AND $this instanceof modSnippet AND $element == $this->get('name')) {
    $properties = $this->getProperties();
    $element = $scriptProperties['element'] = $modx->getOption('element', $properties, 'pdoResources', true);
}

$where = array();
$leftJoin = array();
$innerJoin = array();
$leftJoinFiles = array();
$innerJoinFiles = array();
$select = array(
    $class => "{$class}.*"
);
$groupby = array(
    "{$class}.id",
);

// Add user parameters
foreach (array('where', 'leftJoin', 'innerJoin', 'select', 'groupby') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}

// join Files
foreach (array('leftJoinFiles' => 'leftJoin', 'innerJoinFiles' => 'innerJoin') as $k => $v) {
    if (!empty($scriptProperties[$k])) {
        ${$k} = $userfiles->explodeAndClean($scriptProperties[$k]);
        foreach (${$k} as $var) {
            ${$v}[$var] = array(
                'class' => 'UserFile',
                'on'    => "`{$var}`.class = '{$class}' AND `{$var}`.parent = `{$class}`.id AND `{$var}`.list = '{$var}'",
            );
            $select[$var] = $modx->getSelectColumns('UserFile', $var, $var . '_');

            foreach ($includeThumbs as $thumb) {
                $size = explode('x', $thumb);
                $sizeLike = array();
                if (!empty($size[0])) {
                    $sizeLike[] = 'w\":' . $size[0];
                }
                if (!empty($size[1])) {
                    $sizeLike[] = '"\h\":' . $size[1];
                }
                $sizeLike = implode(',', $sizeLike);

                ${$v}[$thumb] = array(
                    'class' => 'UserFile',
                    'on'    => "`{$thumb}`.class = 'UserFile' AND `{$thumb}`.parent = `{$var}`.id AND `{$thumb}`.list = '{$var}' AND `{$thumb}`.properties LIKE '%{$sizeLike}%'",
                );
                $select[$thumb] = $modx->getSelectColumns('UserFile', $thumb, $thumb . '_');
            }
        }
    }
    unset($scriptProperties[$v]);
}

// where Files
if (!empty($scriptProperties['whereFiles'])) {
    $Files = json_decode($scriptProperties['whereFiles'], true);
    foreach ($Files as $key => $value) {
        $var = preg_replace('#\:.*#', '', $key);
        $key = str_replace($var, $var . '.value', $key);
        if (in_array($var, $leftJoinFiles) OR in_array($var, $innerJoinFiles)) {
            $where[$key] = $value;
        }
    }
}

$default = array(
    'class'     => $class,
    'where'     => $where,
    'leftJoin'  => $leftJoin,
    'innerJoin' => $innerJoin,
    'select'    => $select,
    'sortby'    => "{$class}.id",
    'sortdir'   => 'ASC',
    'groupby'   => implode(', ', $groupby),
    'return'    => $return,
);

$output = '';
/** @var modSnippet $snippet */
if ($snippet = $modx->getObject('modSnippet', array('name' => $element))) {
    $scriptProperties = array_merge($default, $scriptProperties);
    $snippet->setCacheable(false);
    $output = $snippet->process($scriptProperties);
}

return $output;