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

/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService('miniShop2');
$miniShop2->initialize($modx->context->key);

/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$limit = $modx->getOption('limit', $scriptProperties, 0);
$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.ms2.gallery');
$mime = $modx->getOption('mime', $scriptProperties, 'image%', true);
$extensionsDir = $modx->getOption('extensionsDir', $scriptProperties, 'components/minishop2/img/mgr/extensions/', true);

/** @var msProduct $product */
$product = !empty($product) && $product != $modx->resource->id
    ? $modx->getObject('msProduct', $product)
    : $modx->resource;
if (!$product OR !($product instanceof msProduct)) {
    return "[msGallery] The resource with id = {$product->id} is not instance of msProduct.";
}

$class = 'UserFile';
$list = $modx->getOption('userfiles_list_template_' . $product->get('template'), null,
    $modx->getOption('userfiles_list_default', null, 'default', true), true);


$where = array(
    'parent'    => $product->id,
    'list'      => $list,
    'class:!='  => $class,
    'mime:LIKE' => "{$mime}"
);

$leftJoin = array();

$select = array(
    $class => '*',
);

// Include thumbnails
if (!empty($includeThumbs)) {
    $thumbs = $userfiles->explodeAndClean($includeThumbs);
    foreach ($thumbs as $thumb) {
        $size = explode('x', $thumb);
        $sizeLike = array();
        if (!empty($size[0])) {
            $sizeLike[] = 'w\":' . $size[0];
        }
        if (!empty($size[1])) {
            $sizeLike[] = '"\h\":' . $size[1];
        }
        $sizeLike = implode(',', $sizeLike);

        $leftJoin[$thumb] = array(
            'class' => $class,
            'on'    => "{$thumb}.class = 'UserFile' AND {$thumb}.parent = {$class}.id AND {$thumb}.list = '{$list}' AND {$thumb}.properties LIKE '%{$sizeLike}%'",
        );
        $select[$thumb] = "`{$thumb}`.url as `{$thumb}`";
    }
}

// Add user parameters
foreach (array('where') as $v) {
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
$pdoFetch->addTime('Conditions prepared');

$default = array(
    'class'             => $class,
    'where'             => $where,
    'leftJoin'          => $leftJoin,
    'select'            => $select,
    'limit'             => $limit,
    'sortby'            => 'rank',
    'sortdir'           => 'ASC',
    'fastMode'          => false,
    'return'            => 'data',
    'nestedChunkPrefix' => 'userfiles_',
);
// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();
$pdoFetch->addTime('Fetching thumbnails');


// Processing rows
$files = array();
foreach ($rows as $row) {
    $row['product_id'] = $row['parent'];
    $files[] = $row;
}

$output = $pdoFetch->getChunk($tpl, array(
    'files' => $files,
));
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="msGalleryLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}