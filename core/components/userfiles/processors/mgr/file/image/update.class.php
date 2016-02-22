<?php

require_once dirname(dirname(__FILE__)) . '/upload.class.php';

class modUserFileImageUpdateProcessor extends modUserFileUploadProcessor
{
    public function beforeSet()
    {
        $this->mediaSource->errors = array();
        $this->mediaSource->removeObject($this->object->get('path') . $this->object->get('file'));
        if (empty($this->mediaSource->errors['file'])) {
            $file = rtrim(str_replace($this->object->get('type'), '', $this->object->get('file')), '.');
            $type = 'png';

            $this->data['name'] = $file;
            $this->data['type'] = $type;
            $this->data['properties'] = str_replace('blob', $type, $this->data['properties']);

            $this->setProperty('parent', $this->object->get('parent'));
            $this->setProperty('class', $this->object->get('class'));
            $this->setProperty('list', $this->object->get('list'));
        }

        return parent::beforeSet();
    }

}

return 'modUserFileImageUpdateProcessor';