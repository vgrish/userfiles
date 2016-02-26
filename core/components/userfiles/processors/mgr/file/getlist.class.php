<?php

class modUserFileGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'UserFile';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('userfiles');
    public $permission = 'userfiles_file_list';

    /** @var UserFile $object */
    public $object;
    /** @var UserFiles $UserFiles */
    public $UserFiles;
    /** @var modMediaSource $mediaSource */
    public $mediaSource;

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }
        $this->UserFiles = $this->modx->getService('userfiles');
        $this->UserFiles->initialize();

        return parent::initialize();
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();

        return $this->outputArray($data['results'], $data['total']);
    }


    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['results'][] = $this->prepareArray($row);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), true));
        }

        return $data;
    }

    /** {@inheritDoc} */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modMediaSource', 'Source');
        $c->leftJoin($this->classKey, 'Thumbnail',
            "{$this->classKey}.id = Thumbnail.parent AND Thumbnail.class = '{$this->classKey}' AND Thumbnail.rank = 0");
        $c->groupby($this->classKey . '.id');

        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select(array(
            'source_name' => 'Source.name',
            // 'source_properties' => 'Source.properties',

            'thumbnail' => 'Thumbnail.url',
            'size'      => 'UserFile.size'
        ));

        $c->where(array("{$this->classKey}.class:!=" => $this->classKey));
        $c->sortby("{$this->classKey}.rank ASC, {$this->classKey}.createdon", 'ASC');

        $source = $this->getProperty('source');
        if ($source) {
            $c->where(array("{$this->classKey}.source" => $source));
        }

        $list = $this->getProperty('list');
        if ($list) {
            $c->where(array("{$this->classKey}.list" => $list));
        }

        $type = $this->getProperty('type');
        if ($type) {
            $c->where(array("{$this->classKey}.type" => $type));
        }

        $parent = $this->getProperty('parent');
        if ($parent) {
            $c->where(array("{$this->classKey}.parent" => $parent));
        }

        $class = $this->getProperty('class');
        if ($class) {
            $c->where(array("{$this->classKey}.class" => $class));
        }

        $active = $this->getProperty('active');
        if ($active) {
            $c->where(array("{$this->classKey}.active" => $active));
        }

        $createdby = $this->getProperty('createdby');
        if ($createdby) {
            $c->where(array("{$this->classKey}.createdby" => $createdby));
        }

        $session = $this->getProperty('session');
        if ($session) {
            $c->where(array("{$this->classKey}.session" => $session));
        }

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where(array(
                "{$this->classKey}.file:LIKE"           => "%{$query}%",
                "OR:{$this->classKey}.name:LIKE"        => "%{$query}%",
                "OR:{$this->classKey}.description:LIKE" => "%{$query}%",
                "OR:{$this->classKey}.parent:LIKE"      => "%{$query}%",
                "OR:{$this->classKey}.class:LIKE"       => "%{$query}%",
                "OR:{$this->classKey}.session:LIKE"     => "%{$query}%"
            ));
        }

        return $c;
    }


    /** {@inheritDoc} */
    public function prepareArray(array $row)
    {
        $row['active'] = !empty($row['active']);
        $row['cls'] = $row['active'] ? 'active' : 'inactive';


        if (!empty($row['thumbnail'])) {
            $row['dyn_thumbnail'] = $row['thumbnail'] . '?t=' . $row['size'];
        } else {
            $row['dyn_thumbnail'] = null;
        }

        $row['dyn_url'] = $row['url'] . '?t=' . $row['size'];

        $row['format_size'] = $this->UserFiles->Tools->formatFileSize($row['size']);
        $row['format_createdon'] = $this->UserFiles->Tools->formatFileCreatedon($row['createdon']);

        $icon = 'x-menu-item-icon icon';
        $row['actions'] = array();

        $row['actions'][] = array(
            'cls'    => '',
            'icon'   => "$icon $icon-edit",
            'title'  => $this->modx->lexicon('userfiles_action_update'),
            'action' => 'fileUpdate',
            'button' => false,
            'menu'   => true,
        );

        $row['actions'][] = array(
            'cls'    => '',
            'icon'   => "$icon $icon-share",
            'title'  => $this->modx->lexicon('userfiles_action_show'),
            'action' => 'fileShow',
            'button' => false,
            'menu'   => true,
        );

        if (strpos($row['mime'], 'image') !== false) {

            $row['actions'][] = array(
                'cls'    => '',
                'icon'   => "$icon $icon-crop",
                'title'  => $this->modx->lexicon('userfiles_action_edit'),
                'action' => 'fileEdit',
                'button' => false,
                'menu'   => true,
            );

            $row['actions'][] = array(
                'cls'      => '',
                'icon'     => "$icon $icon-refresh",
                'title'    => $this->modx->lexicon('userfiles_action_update_thumbnail'),
                'multiple' => $this->modx->lexicon('userfiles_action_update_thumbnail'),
                'action'   => 'thumbnailCreate',
                'button'   => false,
                'menu'     => true,
            );
        }

        if (!$row['active']) {
            $row['actions'][] = array(
                'cls'      => '',
                'icon'     => "$icon $icon-toggle-off red",
                'title'    => $this->modx->lexicon('userfiles_action_turnon'),
                'multiple' => $this->modx->lexicon('userfiles_action_turnon'),
                'action'   => 'fileTurnOn',
                'button'   => false,
                'menu'     => true,
            );
        } else {
            $row['actions'][] = array(
                'cls'      => '',
                'icon'     => "$icon $icon-toggle-on green",
                'title'    => $this->modx->lexicon('userfiles_action_turnoff'),
                'multiple' => $this->modx->lexicon('userfiles_action_turnoff'),
                'action'   => 'fileTurnOff',
                'button'   => false,
                'menu'     => true,
            );
        }

        $row['actions'][] = array(
            'cls'      => '',
            'icon'     => "$icon $icon-trash-o red",
            'title'    => $this->modx->lexicon('userfiles_action_remove'),
            'multiple' => $this->modx->lexicon('userfiles_action_remove'),
            'action'   => 'fileRemove',
            'button'   => false,
            'menu'     => true,
        );

        return $row;
    }

}

return 'modUserFileGetListProcessor';