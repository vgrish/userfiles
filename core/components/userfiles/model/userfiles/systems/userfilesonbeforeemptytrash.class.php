<?php


class UserFilesOnBeforeEmptyTrash extends UserFilesPlugin
{
    public function run()
    {
        $ids = $this->modx->getOption('ids', $this->scriptProperties);
        $resources = $this->modx->getIterator('modResource', array('id:IN' => $ids));
        /** @var modResource $resource */
        foreach ($resources as $resource) {
            $rid = $resource->get('id');
            $class = $resource->get('class_key');
            $files = $this->modx->getIterator('UserFile', array('parent' => $rid, 'class' => $class));
            /** @var UserFile $file */
            foreach ($files as $file) {
                if ($file->initialized() AND $file->mediaSource) {
                    @$file->mediaSource->removeContainer($file->mediaSource->getBasePath().$file->get('path'));
                }
                $file->remove();
            }
        }

    }

}