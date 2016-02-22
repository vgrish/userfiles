<?php

class modUserFileThumbnailCreateProcessor extends modObjectProcessor
{
    public $classKey = 'UserFile';
    public $languageTopics = array('userfiles');

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$id = $this->getProperty('id')) {
            return $this->failure($this->modx->lexicon('userfiles_err_ns'));
        }

        /* @var UserFile $file */
        if ($file = $this->modx->getObject('UserFile', $id)) {
            $children = $file->getMany('Children');
            /* @var UserFile $child */
            foreach ($children as $child) {
                $child->remove();
            }

            $file->generateThumbnails();
        }

        return $this->success();
    }
}

return 'modUserFileThumbnailCreateProcessor';