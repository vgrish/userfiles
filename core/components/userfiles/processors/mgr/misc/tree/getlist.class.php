<?php

class modUserFilesTreeGetListProcessor extends modProcessor
{
    /** @var modMediaSource|modFileMediaSource $source */
    public $source;
    /** @var modFileHandler */
    public $fileHandler;

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize()
    {
        parent::initialize();
        $options = array();
        $this->fileHandler = $this->modx->getService('fileHandler', 'modFileHandler', '', $options);

        return true;
    }

    public function checkPermissions()
    {
        return $this->modx->hasPermission('file_view');
    }

    public function getLanguageTopics()
    {
        return array('file');
    }

    /**
     * @return array|string
     */
    public function process()
    {
        $files = array();

        $loaded = $this->getSource();
        if ($loaded !== true) {
            return $loaded;
        }

        $path = $this->getProperty('path');
        $type = $this->getProperty('type');
        switch ($type) {
            case 'file':
                $list = $this->source->getObjectContents($path);
                if (is_array($list) AND empty($this->source->errors['file'])) {
                    $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
                    $files[] = array(
                        'name'         => $list['basename'],
                        'size'         => $list['size'],
                        'path'         => $list['path'],
                        'pathRelative' => $list['name'],
                        'ext'          => $fileExtension,
                        'accepted'     => true,
                        'tree'         => true,
                    );
                }
                break;
            case 'dir':
                $list = $this->source->getObjectsInContainer($path);
                if (is_array($list) AND empty($this->source->errors['file'])) {
                    array_filter($list, function (&$row) use (&$files) {
                        $files[] = array(
                            'name'         => $row['name'],
                            'size'         => $row['size'],
                            'path'         => $row['pathname'],
                            'pathRelative' => $row['pathRelative'],
                            'ext'          => $row['ext'],
                            'accepted'     => true,
                            'tree'         => true,
                        );

                        return true;
                    });
                }
                break;
            default:
                break;
        }

        return $this->modx->error->success('', array('files' => $files));
    }

    /**
     * @return boolean|string
     */
    public function getSource()
    {
        $source = $this->getProperty('source', 1);
        /** @var modMediaSource $source */
        $this->modx->loadClass('sources.modMediaSource');
        $this->source = modMediaSource::getDefaultSource($this->modx, $source);
        if (!$this->source->getWorkingContext()) {
            return $this->modx->lexicon('permission_denied');
        }
        $this->source->setRequestProperties($this->getProperties());

        return $this->source->initialize();
    }

}

return 'modUserFilesTreeGetListProcessor';