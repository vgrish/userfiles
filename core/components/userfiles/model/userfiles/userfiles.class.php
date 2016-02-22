<?php

/**
 * The base class for userfiles.
 */
class UserFiles
{
    /** @var modX $modx */
    public $modx;
    /** @var mixed|null $namespace */
    public $namespace = 'userfiles';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();
    /** @var Tools $Tools */
    public $Tools;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/userfiles/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/userfiles/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'      => $this->namespace,
            'assetsBasePath' => MODX_ASSETS_PATH,
            'assetsBaseUrl'  => MODX_ASSETS_URL,

            'assetsUrl'    => $assetsUrl,
            'cssUrl'       => $assetsUrl . 'css/',
            'jsUrl'        => $assetsUrl . 'js/',
            'imagesUrl'    => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,

            'corePath'     => $corePath,
            'modelPath'    => $corePath . 'model/',
            'handlersPath' => $corePath . 'handlers/',

            'chunksPath'     => $corePath . 'elements/chunks/',
            'templatesPath'  => $corePath . 'elements/templates/',
            'snippetsPath'   => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/'
        ), $config);

        $this->modx->addPackage('userfiles', $this->getOption('modelPath'));
        $this->modx->lexicon->load('userfiles:default');
        $this->namespace = $this->getOption('namespace', $config, 'userfiles');
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
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
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * Loads an instance of Tools
     *
     * @return boolean
     */
    public function loadTools()
    {
        if (!is_object($this->Tools) OR !($this->Tools instanceof UserFilesToolsInterface)) {
            $toolsClass = $this->modx->loadClass('tools.Tools', $this->config['handlersPath'], true, true);
            if ($derivedClass = $this->getOption('class_tools_handler', null, '')) {
                if ($derivedClass = $this->modx->loadClass('tools.' . $derivedClass, $this->config['handlersPath'],
                    true, true)
                ) {
                    $toolsClass = $derivedClass;
                }
            }
            if ($toolsClass) {
                $this->Tools = new $toolsClass($this->modx, $this->config);
            }
        }

        return !empty($this->Tools) AND $this->Tools instanceof UserFilesToolsInterface;
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array  $scriptProperties
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));
        $this->modx->error->reset();
        if (!$this->Tools) {
            $this->loadTools();
        }
        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        //$this->modx->log(1, print_r($this->config, 1));

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {
                    $config = $this->modx->toJSON(array(
                        'assetsBaseUrl' => $this->getOption('assetsBaseUrl', $this->config),
                        'assetsUrl'     => $this->getOption('assetsUrl', $this->config),
                        'actionUrl'     => $this->getOption('actionUrl', $this->config),
                        'defaults'      => array(
                            'yes'      => $this->lexicon('yes'),
                            'no'       => $this->lexicon('no'),
                            'message'  => array(
                                'title' => array(
                                    'success' => $this->lexicon('title_ms_success'),
                                    'error'   => $this->lexicon('title_ms_error'),
                                    'info'    => $this->lexicon('title_ms_info'),

                                ),
                                'error' => array(
                                    'date' => $this->lexicon('error_date'),
                                )
                            ),
                            'confirm'  => array(
                                'title' => array(
                                    'success' => $this->lexicon('title_cms_success'),
                                    'error'   => $this->lexicon('title_cms_error'),
                                    'info'    => $this->lexicon('title_cms_info'),

                                )
                            ),
                            'selector' => array(
                                'view' => $this->getOption('selector_view', $this->config, '.payandsee-view', true)
                            )
                        )
                    ));

                    $script = "<script type=\"text/javascript\">paymentsystemConfig={$config}</script>";
                    if (!isset($this->modx->jscripts[$script])) {
                        $this->modx->regClientStartupScript($script, true);
                    }

                    $this->initialized[$ctx] = true;

                }
                break;
        }

        return true;
    }


    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed|string
     */
    public function getChunk($name = '', array $properties = array())
    {
        if (strpos($name, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $name);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);

            return $chunk->process($properties, $content);
        }

        return $this->modx->getChunk($name, $properties);
    }

    /** @inheritdoc} */
    public function getPropertiesKey(array $properties = array())
    {
        return !empty($properties['propkey']) ? $properties['propkey'] : false;
    }

    /** @inheritdoc} */
    public function saveProperties(array $properties = array())
    {
        return !empty($properties['propkey']) ? $_SESSION[$this->namespace][$properties['propkey']] = $properties : false;
    }

    /** @inheritdoc} */
    public function getProperties($key = '')
    {
        return !empty($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : array();
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }


}