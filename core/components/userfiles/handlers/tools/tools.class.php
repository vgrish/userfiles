<?php


interface UserFilesToolsInterface
{


    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     * @param bool  $skipEmpty
     *
     * @return mixed
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false);

    /**
     * @param       $message
     * @param array $placeholders
     *
     * @return mixed
     */
    public function lexicon($message, $placeholders = array());

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return mixed
     */
    public function failure($message = '', $data = array(), $placeholders = array());

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return mixed
     */
    public function success($message = '', $data = array(), $placeholders = array());

    /**
     * @param modManagerController $controller
     * @param array                $opts
     *
     * @return mixed
     */
    public function addFilesController(modManagerController $controller, array $opts = array());

    /**
     * @param     $bytes
     * @param int $precision
     *
     * @return string
     */
    public function formatFileSize($bytes, $precision = 2);

    /**
     * @param        $time
     * @param string $format
     *
     * @return mixed
     */
    public function formatFileCreatedon($time, $format = '%d.%m.%Y');

}

/**
 * Class Tools
 */
class Tools implements UserFilesToolsInterface
{

    static $xpdo;

    /** @var array $config */
    public $config = array();
    /** @var modX $modx */
    protected $modx;
    /** @var UserFiles $UserFiles */
    protected $UserFiles;
    /** @var $namespace */
    protected $namespace;

    /**
     * @param $modx
     * @param $config
     */
    public function __construct($modx, &$config)
    {
        $this->modx = $modx;
        $this->config =& $config;

        self::$xpdo = $modx;

        $corePath = $this->modx->getOption('userfiles_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
        /** @var UserFiles $UserFiles */
        $this->UserFiles = $this->modx->getService(
            'UserFiles',
            'UserFiles',
            $corePath . 'model/userfiles/',
            array(
                'core_path' => $corePath
            )
        );

        $this->namespace = $this->UserFiles->namespace;
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /** @inheritdoc} */
    public function lexicon($message, $placeholders = array())
    {
        return $this->UserFiles->lexicon($message, $placeholders);
    }

    /** @inheritdoc} */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        return $this->UserFiles->failure($message, $data, $placeholders);
    }

    /** @inheritdoc} */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        return $this->UserFiles->success($message, $data, $placeholders);
    }

    /**
     * @param modManagerController $controller
     * @param array                $opts
     */
    public function addFilesController(modManagerController $controller, array $opts = array())
    {
        $controller->addLexiconTopic('userfiles:default');

        $config = $this->config;
        foreach (array('resource', 'user') as $key) {
            if (isset($config[$key]) AND is_object($config[$key]) AND $config[$key] instanceof xPDOObject) {
                /** @var $config xPDOObject[] */
                $config[$key] = $config[$key]->toArray();
            }
        }

        $config['connector_url'] = $this->config['connectorUrl'];
        $config['source'] = $this->getOption('source_default', null, 1, true);

        if (!empty($opts['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
            $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        }

        if (!empty($opts['config'])) {
            $controller->addHtml("<script type='text/javascript'>userfiles.config={$this->modx->toJSON($config)}</script>");
        }

        if (!empty($opts['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/userfiles.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/combo.js');
        }

        if (!empty($opts['jquery'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/jquery/custom/jquery.js');
        }

        if (!empty($opts['cropper'])) {
            $controller->addCss($this->config['assetsUrl'] . 'vendor/cropper/dist/cropper.css');
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/cropper/dist/cropper.js');
        }

        if (!empty($opts['dropzone'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/dropzone/dist/dropzone.js');
        }

        if (!empty($opts['clipboard'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/clipboard/dist/clipboard.js');
        }

        if (!empty($opts['main'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/main/main.panel.js');
        }

        if (!empty($opts['file'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/file/file.panel.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/file/file.view.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/file/file.window.js');
        }

        if (!empty($opts['inject/resource'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/inject/resource.tab.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/inject/resource.panel.js');
        }

        if (!empty($opts['inject/user'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/inject/user.tab.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/inject/user.panel.js');
        }

    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        return $this->UserFiles->getOption($key, $config, $default, $skipEmpty);
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }

    /**
     * @param modResource $resource
     *
     * @return bool
     */
    public function isWorkingTemplates(modResource $resource)
    {
        return !is_object($resource) ? false : in_array($resource->get('template'),
            $this->explodeAndClean($this->getOption('working_templates')));
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        return $this->UserFiles->explodeAndClean($array, $delimiter);
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        return $this->UserFiles->cleanAndImplode($array, $delimiter);
    }

    /**
     * @param     $bytes
     * @param int $precision
     *
     * @return string
     */
    public function formatFileSize($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @param        $time
     * @param string $format
     *
     * @return string
     */
    public function formatFileCreatedon($time, $format = '%d.%m.%Y')
    {
        $time = preg_match('/^\d+$/', $time)
            ? $time
            : strtotime($time);

        return strftime($format, $time);
    }


//$this->UserFiles->Tools->formatFileCreatedon($data, $format);

}

/*
 * define('APACHE_MIME_TYPES_URL','http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');

 * function generateUpToDateMimeArray($url){
    $s=array();
    foreach(@explode("\n",@file_get_contents($url))as $x)
        if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
            for($i=1;$i<$c;$i++)
                $s[]='&nbsp;&nbsp;&nbsp;\''.$out[1][$i].'\' => \''.$out[1][0].'\'';
    return @sort($s)?'$mime_types = array(<br />'.implode($s,',<br />').'<br />);':false;
}
 */