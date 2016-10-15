<?php

class modUserFileUploadProcessor extends modObjectCreateProcessor
{
    public $classKey = 'UserFile';
    public $objectType = 'UserFile';
    public $primaryKeyField = 'id';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_upload';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var Tools $Tools */
    public $Tools;

    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    /** @var array $mediaSourceProperties */
    public $mediaSourceProperties;
    /** @var null $data */
    protected $data = null;

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }

        $primaryKey = $this->getProperty($this->primaryKeyField, false);
        if ($this->getProperty('crop', false)) {
            if (!$this->object = $this->modx->getObject($this->classKey, $primaryKey)) {
                return $this->modx->lexicon($this->objectType . '_err_nfs',
                    array($this->primaryKeyField => $primaryKey));
            }
        } else {
            $this->object = $this->modx->newObject($this->classKey);
        }

        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();
        $this->Tools = $this->UserFiles->Tools;

        $checkSource = $this->checkSource();
        if ($checkSource !== true) {
            return $this->UserFiles->lexicon('err_source_initialize');
        }

        $checkFile = $this->checkFile();
        if ($checkFile !== true) {
            return $checkFile;
        }

        $this->mainThumbnail();
        $this->prepareClass();

        return true;
    }

    public function prepareClass()
    {
        $class = $this->UserFiles->explodeAndClean($this->getProperty('class', 'modResource'));
        $class = end($class);
        $this->setProperty('class', $class);
    }

    /**
     *
     */
    public function mainThumbnail()
    {
        $mainThumbnail = $this->object->getMainThumbnail();
        $imageExtensions = $this->object->getImageExtensions();
        if (!empty($mainThumbnail) AND !empty($imageExtensions) AND in_array($this->data['type'], $imageExtensions)) {
            $mainThumbnail = array_merge(
                $this->object->imageDefaultThumbnail,
                $mainThumbnail
            );
            if ($mainThumbnail AND $thumbnail = $this->object->makeThumbnail(
                    $mainThumbnail, array('content' => file_get_contents($this->data['tmp_name'])))
            ) {
                if (file_put_contents($this->data['tmp_name'], $thumbnail)) {
                    $this->data = $this->getData(array(
                        'tmp_name' => $this->data['tmp_name'],
                        'name'     => $this->data['name'] . '.' . $this->data['type']
                    ));
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[UserFiles] Could not generate thumbnail for main image');
                }
            }
        }
    }

    /** {@inheritDoc} */
    protected function checkSource()
    {
        $source = $this->getProperty('source', $this->object->get('source'));
        $this->object->set('source', $source);

        if ($initialized = $this->object->initialized()) {
            $this->mediaSource = $this->object->mediaSource;
            $this->mediaSourceProperties = $this->object->mediaSourceProperties;
        }

        return $initialized;
    }

    /**
     * @param array $file
     *
     * @return array|string
     */
    protected function getData(array $file = array())
    {
        $tnm = $this->modx->getOption('tmp_name', $file);
        $name = $this->modx->getOption('name', $file);
        if (!file_exists($tnm)) {
            return $this->UserFiles->lexicon('err_file_ns');
        }

        $size = @filesize($tnm);
        if (!$this->getProperty('crop', false)) {
            $mime = $this->UserFiles->Tools->getFileMimeType($tnm, $name);
        } else {
            $mime = 'image/' . $this->getProperty('type', 'png');
        }

        $tim = getimagesize($tnm);
        $width = $height = 0;
        if (is_array($tim)) {
            $width = $tim[0];
            $height = $tim[1];
        }

        $type = explode('.', $name);
        $type = end($type);
        $name = rtrim(str_replace($type, '', $name), '.');
        $hash = hash_file('sha1', $tnm);

        $data = array(
            'tmp_name'   => $tnm,
            'size'       => $size,
            'mime'       => $mime,
            'type'       => $type,
            'name'       => $name,
            'width'      => $width,
            'height'     => $height,
            'hash'       => $hash,
            'properties' => $this->modx->toJSON(array(
                'w' => $width,
                'h' => $height,
                'f' => $type
            ))
        );

        return $data;
    }

    /**
     * @return bool|string
     */
    protected function checkFile()
    {
        $filePath = $this->getProperty('_file_path');
        $fileName = $this->getProperty('_file_name');
        if (
            !($filePath AND $fileName)
            AND
            (
                empty($_FILES['file'])
                OR
                !file_exists($_FILES['file']['tmp_name'])
                OR
                !is_uploaded_file($_FILES['file']['tmp_name'])
            )
        ) {
            return $this->UserFiles->lexicon('err_file_ns');
        }

        /*if (!file_exists($_FILES['file']['tmp_name']) OR !is_uploaded_file($_FILES['file']['tmp_name'])) {
            return $this->UserFiles->lexicon('err_file_ns');
        }
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->UserFiles->lexicon('err_file_ns');
        }*/

        $this->data = $this->getData(array(
            'tmp_name' => $filePath ? $filePath : $_FILES['file']['tmp_name'],
            'name'     => $fileName ? $fileName : $_FILES['file']['name']
        ));

        return true;
    }

    /** {@inheritDoc} */
    public function beforeSet()
    {
        if (empty($this->data)) {
            return $this->UserFiles->lexicon('err_file_ns');
        }

        foreach (array('tmp_name', 'size', 'mime', 'type', 'name', 'width', 'height', 'hash', 'properties') as $key) {
            $this->setProperty($key, strtolower($this->data[$key]));
        }

        $maxUploadSize = $this->modx->getOption('maxUploadSize', $this->mediaSourceProperties, 0, true);
        if ($this->getProperty('size') > $maxUploadSize) {
            return $this->UserFiles->lexicon('err_file_size');
        }

        $allowedFileTypes = $this->modx->getOption('allowedFileTypes', $this->mediaSourceProperties, '', true);
        $allowedFileTypes = $this->UserFiles->Tools->explodeAndClean($allowedFileTypes);
        if (!in_array($this->getProperty('type'), $allowedFileTypes)) {
            return $this->UserFiles->lexicon('err_file_type');
        }

        $imageNameType = $this->modx->getOption('imageNameType', $this->mediaSourceProperties, 'hash', true);
        switch ($imageNameType) {
            case 'friendly':
                $name = $this->getProperty('name');
                /** @var  modResource $resource */
                $resource = $this->modx->newObject('modResource');
                $name = $resource->cleanAlias($name);
                break;
            case 'hash':
            default:
                $name = $this->getProperty('hash');
                break;
        }

        $this->setProperty('parent', $this->getProperty('parent', 0));
        $this->setProperty('class', $this->getProperty('class', 'modResource'));
        $this->setProperty('list',
            $this->getProperty('list', $this->UserFiles->getOption('list_default', null, 'default', true)));
        $this->setProperty('context', $this->getProperty('context', 'web'));


        $alias = '';
        if ($parent = $this->modx->getObject($this->getProperty('class'), (int)$this->getProperty('parent'))) {
            if ($this->getProperty('class') == 'modResource' AND $classKey = $parent->get('class_key')) {
                $this->setProperty('class', $classKey);
            }

            if (!$alias = $parent->get('alias')) {
                $alias = '';
            }
        }

        $pls = array(
            'pl' => array(
                '{name}',
                '{alias}',
                '{id}',
                '{class}',
                '{list}',
                '{session}',
                '{createdby}',
                '{source}',
                '{context}',
                '{w}',
                '{h}',
                '{q}',
                '{zc}',
                '{bg}',
                '{ext}',
                '{rand}'
            ),
            'vl' => array(
                $name,
                $alias,
                $this->getProperty('parent'),
                $this->getProperty('class'),
                $this->getProperty('list'),
                session_id(),
                $this->modx->user->id,
                $this->getProperty('source'),
                $this->getProperty('context'),
                '',
                '',
                '',
                '',
                '',
                $this->getProperty('type'),
                strtolower(strtr(base64_encode(openssl_random_pseudo_bytes(2)), "+/=", "zzz"))
            )
        );

        $filename = $this->object->getFileName();
        $filename = strtolower(str_replace($pls['pl'], $pls['vl'], $filename));
        $filename = preg_replace('#(\.|\?|!|\(|\)){2,}#', '\1', $filename);

        $this->setProperty('file', $filename);

        return parent::beforeSet();
    }


    /** {@inheritDoc} */
    public function beforeSave()
    {
        if (empty($this->data)) {
            return $this->UserFiles->lexicon('err_file_ns');
        }

        $this->object->set('path', $this->object->getFilePath());

        $dsFields = $this->UserFiles->getOption('duplicate_search_fields', null, 'parent,class,list,hash,source', true);
        $dsFields = $this->UserFiles->explodeAndClean($dsFields);

        $q = $this->modx->newQuery($this->classKey);
        foreach ($dsFields as $k) {
            $q->where(array($k => $this->object->get($k)));
        }

        if (!empty($this->modx->user->id)) {
            $q->where(array(
                'createdby' => $this->modx->user->id,
            ));
        } else {
            $q->where(array(
                'session' => session_id(),
            ));
        }

        if ($this->modx->getCount($this->classKey, $q)) {
            return $this->UserFiles->lexicon('err_file_exists', array('file' => $this->data['name']));
        }

        $path = '';
        foreach (explode('/', rtrim($this->object->get('path'), '/')) as $dir) {
            $path .= $dir . '/';
            $this->mediaSource->createContainer($path, '/');
        }
        $this->mediaSource->createContainer($this->object->get('path'), '/');
        $this->mediaSource->errors = array();
        if ($this->mediaSource instanceof modFileMediaSource) {
            $file = $this->mediaSource->createObject(
                $this->object->get('path'),
                $this->object->get('file'),
                ''
            );
            if ($file) {
                copy($this->data['tmp_name'], urldecode($file));
            }
        } else {


            $file = $this->mediaSource->uploadObjectsToContainer(
                $this->object->get('path'),
                array(
                    array(
                        'name'     => $this->object->get('file'),
                        'tmp_name' => $this->data['tmp_name']
                    )
                )
            );
        }

        if (!$this->getProperty('_file_path')) {
            unlink($this->data['tmp_name']);
        }

        if ($file) {
            $url = $this->mediaSource->getObjectUrl($this->object->get('path') . $this->object->get('file'));
            $this->object->set('url', $url);
        } else {
            return $this->UserFiles->lexicon('err_file_create');
        }

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function afterSave()
    {
        $children = $this->object->getMany('Children');
        /* @var UserFile $child */
        foreach ($children as $child) {
            $child->remove();
        }
        $this->object->generateThumbnails();

        return true;
    }

    public function cleanup()
    {
        $array = $this->object->toArray();
        $array['product_thumb'] = $this->object->updateRanks();

        return $this->success('', $array);
    }

}

return 'modUserFileUploadProcessor';
