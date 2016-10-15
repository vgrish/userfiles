<?php

class modUserFileThumbnailCreateProcessor extends modObjectProcessor
{
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');

    /** @var UserFile $object */
    public $object;

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$id = $this->getProperty('id')) {
            return $this->failure($this->modx->lexicon('userfiles_err_ns'));
        }

        /* @var UserFile $file */
        if ($this->object = $this->modx->getObject('UserFile', $id)) {
            $children = $this->object->getMany('Children');
            /* @var UserFile $child */
            foreach ($children as $child) {
                $child->remove();
            }

            $this->object->generateThumbnails();
        }

        return $this->cleanup();
    }

    public function cleanup()
    {
        $array = $this->object->toArray();
        $array['product_thumb'] = $this->object->updateRanks();

        return $this->success('', $array);
    }
}

return 'modUserFileThumbnailCreateProcessor';