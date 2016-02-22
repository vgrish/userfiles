<?php

class modUserFilesGetLinkProcessor extends modObjectGetProcessor
{
    public $objectType = 'UserFile';
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');
    public $permission = '';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    /** @var array $mediaSourceProperties */
    public $mediaSourceProperties;

    /** {@inheritDoc} */
    public function initialize()
    {
        $initialize = parent::initialize();

        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();

        $checkSource = $this->checkSource();
        if ($checkSource !== true) {
            return $this->UserFiles->lexicon('err_source_initialize');
        }

        return $initialize;
    }

    /** {@inheritDoc} */
    protected function checkSource()
    {
        if ($initialized = $this->object->initialized()) {
            $this->mediaSource = $this->object->mediaSource;
            $this->mediaSourceProperties = $this->object->mediaSourceProperties;
        }

        return $initialized;
    }

    /**
     * @return array|string
     */
    public function cleanup()
    {
        $array = $this->object->toArray();
        $array['link'] = 'main';

        $imageExtensions = $this->object->getImageExtensions();
        if (in_array($this->object->get('type'), $imageExtensions)) {
            $chunk = $this->modx->getOption('userfiles_chunk_link_image');
        } else {
            $chunk = $this->modx->getOption('userfiles_chunk_link_file');
        }

        $key = 'userfiles_chunk_link_' . $this->object->get('type');
        if ($setting = $this->modx->getObject('modSystemSetting', array('key' => $key))) {
            $chunk = $setting->get('value');
        }

        $rows = array($array);
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            'parent' => $array['id']
        ));
        $q->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $q->select(array(
            "link" => "CONCAT_WS('x',
            substring(properties, locate('\"w\":',properties)+4,locate(',\"', properties, locate('\"w\":',properties))-locate('\"w\":',properties)-4),
            substring(properties, locate('\"h\":',properties)+4,locate(',\"', properties, locate('\"h\":',properties))-locate('\"h\":',properties)-4)
            )"
        ));
        $q->sortby("rank", 'ASC');
        $q->limit(0);
        if ($q->prepare() && $q->stmt->execute()) {
            $rows = array_merge($rows, (array)$q->stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        $links = array();
        /** @var modChunk $chunk */
        if ($chunk = $this->modx->getObject('modChunk', $chunk)) {
            $chunk = $chunk->get('name');//$array['link'] = $this->UserFiles->getChunk($chunk->get('name'), $array);
            foreach ($rows as $row) {
                if (empty($row['link'])) {
                    $row['link'] = $row['id'];
                }
                $links[$row['link']] = $this->UserFiles->getChunk($chunk, $row);
            }
        }

        $array['links'] = $links;

        return $this->success('', $array);
    }
}

return 'modUserFilesGetLinkProcessor';