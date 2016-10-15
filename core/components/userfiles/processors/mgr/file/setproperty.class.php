<?php
require_once dirname(__FILE__) . '/update.class.php';

/**
 * SetProperty a UserFile
 */
class modUserFileSetPropertyProcessor extends modUserFileUpdateProcessor
{
    /** @var UserFile $object */
    public $object;
    public $objectType = 'UserFile';
    public $classKey = 'UserFile';
    public $languageTopics = array('mlmsystem');
    public $permission = 'userfiles_file_update';

    /** {@inheritDoc} */
    public function beforeSet()
    {
        $fieldName = $this->getProperty('field_name', null);
        $fieldValue = $this->getProperty('field_value', null);

        $this->properties = array();
        if (!is_null($fieldName) AND !is_null($fieldValue)) {
            $this->setProperty($fieldName, $fieldValue);
        }

        return parent::beforeSet();
    }
}

return 'modUserFileSetPropertyProcessor';