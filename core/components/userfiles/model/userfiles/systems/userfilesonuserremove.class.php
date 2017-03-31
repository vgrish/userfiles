<?php


class UserFilesOnUserRemove extends UserFilesPlugin
{
    public function run()
    {
        $user = $this->modx->getOption('user', $this->scriptProperties);
        $rid = $user->get('id');
        $files = $this->modx->getIterator('UserFile', array('parent' => $rid, 'class' => 'modUser'));
        /** @var UserFile $file */
        foreach ($files as $file) {
            if ($file->initialized() AND $file->mediaSource) {
                @$file->mediaSource->removeContainer($file->mediaSource->getBasePath() . $file->get('path'));
            }
            $file->remove();
        }

    }

}