<?php

class UserFile extends xPDOSimpleObject
{
    /** @var modX $modx */
    public $modx;
    /** @var UserFiles $UserFiles */
    public $UserFiles;

    /* @var modMediaSource $mediaSource */
    public $mediaSource;
    /* @var array $mediaSourceProperties */
    public $mediaSourceProperties;
    /** @var array $initialized */
    public $initialized = array();

    /** @var array $imageThumbnail */
    public $imageDefaultThumbnail = array(
        'q'  => 90,
        'bg' => 'fff',
        'f'  => 'jpg'
    );

    /**
     * UserFile constructor.
     *
     * @param xPDO $xpdo
     */
    public function __construct(xPDO & $xpdo)
    {
        parent:: __construct($xpdo);

        $this->modx = $xpdo;
        $corePath = $this->modx->getOption('userfiles_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
        /** @var UserFiles $UserFiles */
        $this->UserFiles = $this->modx->getService(
            'UserFiles',
            'UserFiles',
            $corePath . 'model/userfiles/',
            array(
                'core_path' => $corePath
            )
        );

        $this->UserFiles->initialize($this->modx->context->key);
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        if (!$this->initialized()) {
            return false;
        }

        $filename = $this->get('path') . $this->get('file');
        if (!@$this->mediaSource->removeObject($filename)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,
                '[UserFiles] Error remove the attachment file at: ' . $filename);
        }

        return parent::remove($ancestors);
    }

    /**
     * @return bool|string
     */
    public function initialized()
    {
        $source = $this->get('source');
        if (!empty($this->initialized[$source])) {
            return true;
        }
        /** @var modMediaSource $mediaSource */
        if ($mediaSource = $this->modx->getObject('sources.modMediaSource', $source)) {
            $mediaSource->set('ctx', $this->get('context'));
            if ($mediaSource->initialize()) {
                $this->mediaSource = $mediaSource;
                $this->mediaSourceProperties = $mediaSource->getPropertyList();
                $this->initialized[$source] = true;

                return true;
            }
        }

        return 'Could not initialize media source with id = ' . $source;
    }

    /**
     * @param array|string $k
     * @param null         $format
     * @param null         $formatTemplate
     *
     * @return mixed|string
     */
    public function get($k, $format = null, $formatTemplate = null)
    {
        switch ($k) {
            case 'format_size':
                $value = $this->formatFileSize($this->get('size'));
                break;
            case 'format_createdon':
                $value = $this->formatFileCreatedon($this->get('createdon'));
                break;
            default:
                $value = parent::get($k, $format, $formatTemplate);
                break;
        }

        return $value;
    }

    /**
     * @param     $bytes
     * @param int $precision
     *
     * @return string
     */
    public function formatFileSize($bytes, $precision = 2)
    {
        return $this->UserFiles->Tools->formatFileSize($bytes, $precision);
    }


    /**
     * @param        $time
     * @param string $format
     *
     * @return string
     */
    public function formatFileCreatedon($time, $format = '%d.%m.%Y')
    {
        return $this->UserFiles->Tools->formatFileCreatedon($time, $format);
    }

    /**
     * @return bool
     */
    public function generateThumbnails()
    {
        if (!$this->initialized()) {
            return false;
        }

        $imageThumbnails = $this->getImageThumbnails();
        if (empty($imageThumbnails)) {
            return false;
        }

        $imageExtensions = $this->getImageExtensions();
        if (!in_array($this->get('type'), $imageExtensions)) {
            return false;
        }

        $thumbnailType = $this->getThumbnailType();
        foreach ($imageThumbnails as $k => $imageThumbnail) {
            $imageThumbnails[$k] = array_merge(
                $this->imageDefaultThumbnail,
                array('f' => $thumbnailType),
                $imageThumbnail
            );
        }

        foreach ($imageThumbnails as $k => $imageThumbnail) {
            if ($thumbnail = $this->makeThumbnail($imageThumbnail)) {
                $this->saveThumbnail($thumbnail, $imageThumbnail);
            }
        }

        return true;
    }

    /**
     * @return array|bool|mixed
     */
    public function getImageThumbnails()
    {
        $value = array();

        if (
            $this->initialized()
            AND
            isset($this->mediaSourceProperties['imageThumbnails'])
        ) {
            $value = $this->xpdo->fromJSON($this->mediaSourceProperties['imageThumbnails']);
        }

        return $value;
    }

    /**
     * @return array|bool
     */
    public function getImageExtensions()
    {
        $value = array();

        if (
            $this->initialized()
            AND
            isset($this->mediaSourceProperties['imageExtensions'])
        ) {
            $value = $this->UserFiles->explodeAndClean($this->mediaSourceProperties['imageExtensions']);
        }

        return $value;
    }

    /**
     * @return bool|string
     */
    public function getThumbnailType()
    {
        $value = 'jpg';

        if (
            $this->initialized()
            AND
            isset($this->mediaSourceProperties['thumbnailType'])
            AND
            !empty($this->mediaSourceProperties['thumbnailType'])
        ) {
            $value = strtolower($this->mediaSourceProperties['thumbnailType']);
        }

        return $value;
    }


    /**
     * @return bool|mixed
     */
    public function getMainThumbnail()
    {
        $value = false;

        if (
            $this->initialized()
            AND
            isset($this->mediaSourceProperties['imageMainThumbnail'])
            AND
            !empty($this->mediaSourceProperties['imageMainThumbnail'])
        ) {
            $value = $this->modx->fromJSON($this->mediaSourceProperties['imageMainThumbnail']);
        }

        return $value;
    }

    /**
     * @param array $options
     *
     * @return bool|null
     */
    public function makeThumbnail($options = array(), $contents = null)
    {
        $this->mediaSource->errors = array();
        $filename = $this->get('path') . $this->get('file');
        $contents = $contents ?: $this->mediaSource->getObjectContents($filename);

        if (!is_array($contents)) {
            return "[UserFiles] Could not retrieve contents of file {$filename} from media source.";
        } elseif (!empty($this->mediaSource->errors['file'])) {
            return "[UserFiles] Could not retrieve file {$filename} from media source: " . $this->mediaSource->errors['file'];
        }

        if (!class_exists('modPhpThumb')) {
            /** @noinspection PhpIncludeInspection */
            require MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';
        }
        /** @noinspection PhpParamsInspection */
        $phpThumb = new modPhpThumb($this->xpdo);
        $phpThumb->initialize();

        $cacheDir = $this->xpdo->getOption('userfiles_phpThumb_config_cache_directory', null,
            MODX_CORE_PATH . 'cache/phpthumb/');
        /* check to make sure cache dir is writable */
        if (!is_writable($cacheDir)) {
            if (!$this->xpdo->cacheManager->writeTree($cacheDir)) {
                $this->xpdo->log(modX::LOG_LEVEL_ERROR, '[phpThumbOf] Cache dir not writable: ' . $cacheDir);

                return false;
            }
        }

        $phpThumb->setParameter('config_cache_directory', $cacheDir);
        $phpThumb->setParameter('config_cache_disable_warning', true);
        $phpThumb->setParameter('config_allow_src_above_phpthumb', true);
        $phpThumb->setParameter('config_allow_src_above_docroot', true);
        $phpThumb->setParameter('allow_local_http_src', true);
        $phpThumb->setParameter('config_document_root', $this->xpdo->getOption('base_path', null, MODX_BASE_PATH));
        $phpThumb->setParameter('config_temp_directory', $cacheDir);
        $phpThumb->setParameter('config_max_source_pixels',
            $this->xpdo->getOption('userfiles_phpThumb_config_max_source_pixels', null, '26843546'));

        $phpThumb->setCacheDirectory();

        $phpThumb->setSourceData($contents['content']);
        foreach ($options as $k => $v) {
            $phpThumb->setParameter($k, $v);
        }

        if ($phpThumb->GenerateThumbnail()) {
            if (!is_resource($phpThumb->gdimg_output)) {
                $this->xpdo->log(modX::LOG_LEVEL_ERROR,
                    '[UserFiles] phpThumb messages for "' . $this->get('url') . 'failed because !is_resource($phpThumb->gdimg_output)');

            } else {
                if ($phpThumb->RenderOutput()) {
                    return $phpThumb->outputImageData;
                }
            }
        }
        $this->xpdo->log(modX::LOG_LEVEL_ERROR,
            '[UserFiles] Could not generate thumbnail for "' . $this->get('url') . ' ' . __FILE__ . __LINE__);

        return false;
    }

    /**
     * @param       $thumbnail
     * @param array $options
     *
     * @return bool
     */
    public function saveThumbnail($thumbnail, $options = array())
    {
        $pls = array(
            'pl' => array(
                '{name}',
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
                rtrim(str_replace($this->get('type'), '', $this->get('file')), '.'),
                $this->get('id'),
                $this->get('class'),
                $this->get('list'),
                $this->get('session'),
                $this->get('createdby'),
                $this->get('source'),
                $this->get('context'),
                $options['w'],
                $options['h'],
                $options['q'],
                $options['zc'],
                $options['bg'],
                $options['f'],
                strtolower(strtr(base64_encode(openssl_random_pseudo_bytes(2)), '+/=', 'zzz'))
            )
        );

        $thumbnailName = $this->getThumbnailName();
        $filename = strtolower(str_replace($pls['pl'], $pls['vl'], $thumbnailName));
        $filename = preg_replace('#(\.|\?|!|\(|\)){2,}#', '\1', $filename);

        /** @var UserFile $thumbnailFile */
        $thumbnailFile = $this->xpdo->newObject('UserFile', array_merge(
            $this->toArray('', true),
            array(
                'class'  => 'UserFile',
                'parent' => $this->get('id'),
                'file'   => $filename,
                'hash'   => sha1($thumbnail)
            )
        ));

        $this->mediaSource->createContainer($thumbnailFile->get('path'), '/');
        $file = $this->mediaSource->createObject(
            $thumbnailFile->get('path'),
            $thumbnailFile->get('file'),
            $thumbnail
        );

        if ($file) {

            $size = strlen($thumbnail);
            $mime = $this->UserFiles->Tools->getFileMimeTypeFromBinary($thumbnail);

            $thumbnailFile->set('size', $size);
            $thumbnailFile->set('mime', $mime);
            $thumbnailFile->set('type', $options['f']);
            $thumbnailFile->set('properties', $this->modx->toJSON($options));
            $thumbnailFile->set('url',
                $this->mediaSource->getObjectUrl($thumbnailFile->get('path') . $thumbnailFile->get('file')));

            return $thumbnailFile->save();
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        $path = array();
        foreach (array('list', 'class', 'parent') as $k) {
            $path[] = $this->get($k);
        }
        $path[] = null;

        return $path = strtolower(implode('/', $path));
    }

    /**
     * @return bool
     */
    public function isMove()
    {
        return $this->get('class') != 'UserFile' AND $this->get('path') != $this->getFilePath();
    }

    /**
     * @return bool|string
     */
    public function getFileName()
    {
        if (!$this->initialized()) {
            return false;
        }
        if (isset($this->mediaSourceProperties['fileName']) AND !empty($this->mediaSourceProperties['fileName'])) {
            return $this->mediaSourceProperties['fileName'];
        }

        return '{name}.{rand}.{ext}';
    }

    /**
     * @return bool|string
     */
    public function getThumbnailName()
    {
        if (!$this->initialized()) {
            return false;
        }
        if (isset($this->mediaSourceProperties['thumbnailName']) AND !empty($this->mediaSourceProperties['thumbnailName'])) {
            return $this->mediaSourceProperties['thumbnailName'];
        }

        return '{name}.{rand}.{w}.{h}.{ext}';
    }

    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        if ($this->isNew()) {
            if (!$this->get('list')) {
                $this->set('list', 'default');
            }
            if (!$this->get('session')) {
                $this->set('session', session_id());
            }
            if (!$this->get('class')) {
                $this->set('class', 'modResource');
            }
            if (!$this->get('createdon')) {
                $this->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            }
            if (!$this->get('createdby')) {
                if (!empty($this->modx->user) AND $this->modx->user instanceof modUser) {
                    $this->set('createdby', $this->modx->user->get('id'));
                }
            }
        }

        if ($this->isMove() AND $this->initialized()) {

            $this->set('move', true);
            $this->mediaSource->errors = array();
            $filename = $this->get('path') . $this->get('file');
            $contents = $this->mediaSource->getObjectContents($filename);

            if (!is_array($contents)) {
                return "[UserFiles] Could not retrieve contents of file {$filename} from media source.";
            } elseif (!empty($this->mediaSource->errors['file'])) {
                return "[UserFiles] Could not retrieve file {$filename} from media source: " . $this->mediaSource->errors['file'];
            }

            $newPath = $this->getFilePath();
            $this->mediaSource->createContainer($newPath, '/');
            if ($this->mediaSource->createObject($newPath, $this->get('file'), $contents['content'])) {
                $this->mediaSource->removeObject($filename);

                $this->set('path', $newPath);
                $this->set('url', $this->mediaSource->getObjectUrl($newPath . $this->get('file')));
            }

        }

        if ($this->isNew() || $this->get('move')) {
            $q = $this->xpdo->newQuery('UserFile');
            $q->where(array(
                'parent'  => $this->get('parent'),
                'class'   => $this->get('class'),
                'source'  => $this->get('source'),
                'context' => $this->get('context')
            ));
            $this->set('rank', $this->xpdo->getCount('UserFile', $q));
        }

        $saved = parent:: save($cacheFlag);

        return $saved;
    }


    /**
     * @param string $alias
     * @param null   $criteria
     * @param bool   $cacheFlag
     *
     * @return array
     */
    public function & getMany($alias, $criteria = null, $cacheFlag = true)
    {
        if ($alias == 'Children') {
            $criteria = array('class' => 'UserFile');
        }

        return parent::getMany($alias, $criteria, $cacheFlag);
    }

}