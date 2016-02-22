<?php

class modUserFileSortProcessor extends modObjectProcessor
{
    public $classKey = 'UserFile';
    public $permission = '';

    /**
     * from
     * https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php
     *
     * @return array|string
     */
    public function process()
    {
        /* @var UserFile $source */
        $source = $this->modx->getObject($this->classKey, $this->getProperty('source'));
        /* @var UserFile $target */
        $target = $this->modx->getObject($this->classKey, $this->getProperty('target'));

        if (!$source OR !$target) {
            return $this->failure('');
        }

        foreach (array('parent', 'class', 'source', 'context') as $key) {
            $this->setProperty($key, $source->get($key));
        }

        if (
            $this->getProperty('parent') != $target->get('parent') OR
            $this->getProperty('class') != $target->get('class') OR
            $this->getProperty('source') != $target->get('source') OR
            $this->getProperty('context') != $target->get('context')
        ) {
            return $this->failure('');
        }

        if ($source->get('rank') < $target->get('rank')) {
            $this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = rank - 1 WHERE
					parent = {$this->getProperty('parent')}
					AND class = '{$this->getProperty('class')}'
					AND source = {$this->getProperty('source')}
					AND context = '{$this->getProperty('context')}'

					AND rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");
            $newRank = $target->get('rank');
        } else {
            $this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = rank + 1 WHERE
					parent = {$this->getProperty('parent')}
					AND class = '{$this->getProperty('class')}'
					AND source = {$this->getProperty('source')}
					AND context = '{$this->getProperty('context')}'

					AND rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
            $newRank = $target->get('rank');
        }

        $source->set('rank', $newRank);
        $source->save();

        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            "{$this->classKey}.rank"    => 0,
            "{$this->classKey}.parent"  => $this->getProperty('parent'),
            "{$this->classKey}.class"   => $this->getProperty('class'),
            "{$this->classKey}.source"  => $this->getProperty('source'),
            "{$this->classKey}.context" => $this->getProperty('context')
        ));

        if (1 != $this->modx->getCount($this->classKey, $q)) {
            $this->setRanks();
        }

        return $this->success('');
    }

    /** {@inheritDoc} */
    public function setRanks()
    {
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array(
            "{$this->classKey}.parent"  => $this->getProperty('parent'),
            "{$this->classKey}.class"   => $this->getProperty('class'),
            "{$this->classKey}.source"  => $this->getProperty('source'),
            "{$this->classKey}.context" => $this->getProperty('context')
        ));
        $q->select('id');
        $q->sortby('rank ASC, id', 'ASC');
        if ($q->prepare() AND $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $sql = '';
            $table = $this->modx->getTableName($this->classKey);
            foreach ($ids as $k => $id) {
                $sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `id` = '{$id}';";
            }
            $this->modx->exec($sql);
        }
    }

}

return 'modUserFileSortProcessor';
