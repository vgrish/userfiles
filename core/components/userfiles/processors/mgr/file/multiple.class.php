<?php

/**
 * Multiple a UserFile
 */
class modUserFileMultipleProcessor extends modProcessor
{
    public $classKey = 'UserFile';
    public $permission = 'userfiles_file_list';

    public function process()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }

        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = $this->modx->fromJSON($this->getProperty('ids'));

        if (!empty($ids)) {
            foreach ($ids as $id) {
                if (!empty($id)) {
                    if ($response = $this->modx->runProcessor($method,
                        array(
                            'id'          => $id,
                            'field_name'  => $this->getProperty('field_name', null),
                            'field_value' => $this->getProperty('field_value', null)
                        ),
                        array('processors_path' => dirname(__FILE__) . '/')
                    )
                    ) {
                        if ($response->isError()) {
                            return $response->getResponse();
                        }
                    }
                }
            }
        } elseif ($this->getProperty('field_name') == 'false') {
            if ($response = $this->modx->runProcessor($method,
                array(),
                array('processors_path' => dirname(__FILE__) . '/')
            )
            ) {
                if ($response->isError()) {
                    return $response->getResponse();
                }
            }
        }

        return $this->success('');
    }
}

return 'modUserFileMultipleProcessor';