<?php

require_once dirname(dirname(dirname(__FILE__))) . '/mgr/file/getlist.class.php';

class modWebUserFileGetListProcessor extends modUserFileGetListProcessor
{
    public $classKey = 'UserFile';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('userfiles');
    public $permission = '';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var modMediaSource $mediaSource */
    public $mediaSource;

    /** {@inheritDoc} */
    public function initialize()
    {
        $initialize =  parent::initialize();

        $this->modx->log(1, print_r('=======' ,1));
       // $this->modx->log(1, print_r($this->getProperties() ,1));

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->UserFiles->lexicon('err_propkey_ns');
        }

        $properties = $this->UserFiles->getProperties($propKey);
        if (empty($properties)) {
            return $this->UserFiles->lexicon('err_properties_ns');
        }

        $this->modx->log(1, print_r($properties ,1));

        foreach (array('class', 'parent', 'list', 'createdby') as $key) {
            $this->setProperty($key, $properties[$key]);
        }

        /*
         * [tplForm] => uf.form
    [class] => modResource
    [parent] =>
    [list] => default
    [allowAnonym] =>
    [sortby] => rank
    [sortdir] => ASC
    [objectName] => UserFilesForm
    [frontendCss] => [[+assetsUrl]]css/web/default.css
    [frontendJs] => [[+assetsUrl]]js/web/default.js
    [jqueryJs] => [[+assetsUrl]]vendor/jquery/dist/jquery.min.js
    [actionUrl] => [[+assetsUrl]]action.php
    [toPlaceholder] =>
    [dropzone] => Array
        (
            [addRemoveLinks] => 1
        )

    [selector] => .userfiles-form
    [propkey] => 25b61462cc9eac7b164238226c476393a14c0b66
         */

        return $initialize;
    }

    /** {@inheritDoc} */
    public function prepareArray(array $row)
    {
        $row['active'] = !empty($row['active']);
        $row['cls'] = $row['active'] ? 'active' : 'inactive';


        if (!empty($row['thumbnail'])) {
            $row['dyn_thumbnail'] = $row['thumbnail'] . '?t=' . $row['size'];
        }
        $row['dyn_url'] = $row['url'] . '?t=' . $row['size'];

        $row['format_size'] = $this->UserFiles->Tools->formatFileSize($row['size']);
        $row['format_createdon'] = $this->UserFiles->Tools->formatFileCreatedon($row['createdon']);

        return $row;
    }

}

return 'modWebUserFileGetListProcessor';