<?php

class modUserFileSortProcessor extends modObjectProcessor
{
    public $classKey = 'UserFile';
    public $permission = 'userfiles_file_update';

    /**
     * from
     * https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php
     *
     * @return array|string
     */
    public function process()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('userfiles_err_permission_denied');
        }

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

        $table = $this->modx->getTableName($this->classKey);

        if ($source->get('rank') < $target->get('rank')) {
            $this->modx->exec("UPDATE {$table}
				SET rank = rank - 1 WHERE
					parent = '{$this->getProperty('parent')}'
					AND class = '{$this->getProperty('class')}'
					AND source = '{$this->getProperty('source')}'
					AND context = '{$this->getProperty('context')}'

					AND rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");
            $newRank = $target->get('rank');
        } else {
            $this->modx->exec("UPDATE {$table}
				SET rank = rank + 1 WHERE
					parent = '{$this->getProperty('parent')}'
					AND class = '{$this->getProperty('class')}'
					AND source = '{$this->getProperty('source')}'
					AND context = '{$this->getProperty('context')}'

					AND rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
            $newRank = $target->get('rank');
        }

        $source->set('rank', $newRank);
        $source->save();

        $thumb = $source->updateRanks();

        return $this->success('', array('product_thumb' => $thumb));
    }

}

return 'modUserFileSortProcessor';
