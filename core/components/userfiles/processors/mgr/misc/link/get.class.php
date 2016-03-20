<?php

class modUserFilesGetLinkProcessor extends modProcessor
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
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();

        $this->object = $this->modx->newObject($this->classKey);
        $this->object->set('source', $this->getProperty('source'));

        $checkSource = $this->checkSource();
        if ($checkSource !== true) {
            return $this->UserFiles->lexicon('err_source_initialize');
        }

        return true;
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

    public function process()
    {
        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        $rows = array();

        /* main link */
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            'id:IN' => $ids
        ));
        $q->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $q->select(array(
            "link" => "CONCAT_WS('', 'main','')"
        ));
        $q->sortby("FIELD({$this->classKey}.id, " . implode(',', $ids) . ")");
        $q->limit(0);
        if ($q->prepare() && $q->stmt->execute()) {
            $rows = (array)$q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /* child link */
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            'OR:parent:IN' => $ids
        ));

        $q->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $q->select(array(
            "link" => "CONCAT_WS('x',
            substring(properties, locate('\"w\":',properties)+4,locate(',\"', properties, locate('\"w\":',properties))-locate('\"w\":',properties)-4),
            substring(properties, locate('\"h\":',properties)+4,locate(',\"', properties, locate('\"h\":',properties))-locate('\"h\":',properties)-4)
            )"
        ));
        $q->sortby("rank", 'ASC');
        $q->sortby("FIELD({$this->classKey}.parent, " . implode(',', $ids) . ")");
        $q->limit(0);
        if ($q->prepare() && $q->stmt->execute()) {
            $rows = array_merge($rows, (array)$q->stmt->fetchAll(PDO::FETCH_ASSOC));
        }


        $links = array();
        $imageExtensions = $this->object->getImageExtensions();

        foreach ($rows as $row) {
            if (in_array($row['type'], $imageExtensions)) {
                $chunk = $this->modx->getOption('userfiles_chunk_link_image');
            } else {
                $chunk = $this->modx->getOption('userfiles_chunk_link_file');
            }

            /** @var modChunk $chunk */
            if ($chunk = $this->modx->getObject('modChunk', $chunk)) {
                $chunk = $chunk->get('name');
                if (empty($row['link'])) {
                    $row['link'] = $row['id'];
                }
                $links[$row['link']][] = $this->UserFiles->getChunk($chunk, $row);
            }
        }

        return $this->success('', array('links' => $links));
    }

}

return 'modUserFilesGetLinkProcessor';