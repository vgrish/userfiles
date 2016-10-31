<?php
$xpdo_meta_map['UserFile']= array (
  'package' => 'userfiles',
  'version' => '1.1',
  'table' => 'user_files',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'parent' => 0,
    'class' => '',
    'list' => '',
    'name' => NULL,
    'description' => NULL,
    'path' => '',
    'file' => '',
    'mime' => '',
    'type' => '',
    'url' => '',
    'hash' => '',
    'session' => '',
    'size' => 0,
    'createdby' => 0,
    'source' => 1,
    'context' => 'web',
    'active' => 1,
    'rank' => 0,
    'createdon' => NULL,
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'list' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'path' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'file' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'mime' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'hash' => 
    array (
      'dbtype' => 'char',
      'precision' => '40',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'session' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'size' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'source' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'context' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'web',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
    ),
    'rank' => 
    array (
      'dbtype' => 'smallint',
      'precision' => '3',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'parent' => 
    array (
      'alias' => 'parent',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'class' => 
    array (
      'alias' => 'class',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'class' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'list' => 
    array (
      'alias' => 'list',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'list' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'path' => 
    array (
      'alias' => 'path',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'path' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'mime' => 
    array (
      'alias' => 'mime',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'mime' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'type' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'hash' => 
    array (
      'alias' => 'hash',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'hash' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'session' => 
    array (
      'alias' => 'session',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'session' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'createdby' => 
    array (
      'alias' => 'createdby',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdby' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'source' => 
    array (
      'alias' => 'source',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'source' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'context' => 
    array (
      'alias' => 'context',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'context' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Children' => 
    array (
      'class' => 'UserFile',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'UserFile',
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Parent' => 
    array (
      'class' => 'UserFile',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'UserFile',
        ),
      ),
    ),
    'Source' => 
    array (
      'class' => 'modMediaSource',
      'local' => 'source',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Context' => 
    array (
      'class' => 'modContext',
      'local' => 'context',
      'foreign' => 'key',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Resource' => 
    array (
      'class' => 'modResource',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'modResource',
        ),
      ),
    ),
    'User' => 
    array (
      'class' => 'modUser',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'modUser',
        ),
      ),
    ),
    'Product' => 
    array (
      'class' => 'msProduct',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'msProduct',
        ),
      ),
    ),
  ),
);
