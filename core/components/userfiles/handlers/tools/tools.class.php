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
    public function loadControllerJsCss(modManagerController $controller, array $opts = array());

    /**
     * @param array $opts
     */
    public function loadResourceJsCss(array $opts = array());

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

    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed
     */
    public function getChunk($name = '', array $properties = array());


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
     * @param array $opts
     */
    public function loadResourceJsCss(array $opts = array())
    {
        $opts = array_merge($this->config, $opts);
        $pls = $this->makePlaceholders($opts);

        if ($opts['jqueryJs']) {
            $this->modx->regClientScript(preg_replace($this->config['replacePattern'], '', '
				<script type="text/javascript">
					if(typeof jQuery == "undefined") {
						document.write("<script src=\"' . str_replace($pls['pl'], $pls['vl'], $opts['jqueryJs']) . '\" type=\"text/javascript\"><\/script>");
					}
   				</script>
			'), true);
        }
        if ($opts['frontendJs']) {
            $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $opts['frontendJs']));
        }
        if ($opts['frontendCss']) {
            $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $opts['frontendCss']));
        }

        $config = $this->modx->toJSON(array(
            'assetsBaseUrl' => str_replace($pls['pl'], $pls['vl'], $opts['assetsBaseUrl']),
            'assetsUrl'     => str_replace($pls['pl'], $pls['vl'], $opts['assetsUrl']),
            'actionUrl'     => str_replace($pls['pl'], $pls['vl'], $opts['actionUrl']),
            'dropzone'      => (array)$opts['dropzone'],
            'modal'         => (array)$opts['modal'],
            'propkey'       => "{$this->config['propkey']}",
            'ctx'           => "{$this->modx->context->get('key')}"
        ));
        $this->modx->regClientScript(preg_replace($this->config['replacePattern'], '', '
			<script type="text/javascript">
				' . trim($opts['objectName']) . '.initialize(' . $config . ');
   			</script>
		'), true);
    }

    /**
     * @param array  $array
     * @param string $plPrefix
     * @param string $prefix
     * @param string $suffix
     * @param bool   $uncacheable
     *
     * @return array
     */
    public function makePlaceholders(
        array $array = array(),
        $plPrefix = '',
        $prefix = '[[+',
        $suffix = ']]',
        $uncacheable = true
    ) {
        return $this->UserFiles->makePlaceholders($array, $plPrefix, $prefix, $suffix, $uncacheable);
    }

    /**
     * @param modManagerController $controller
     * @param array                $opts
     */
    public function loadControllerJsCss(modManagerController $controller, array $opts = array())
    {
        $controller->addLexiconTopic('userfiles:default');

        $config = $this->config;
        foreach (array('resource', 'user') as $key) {
            if (isset($config[$key]) AND is_object($config[$key]) AND $config[$key] instanceof xPDOObject) {
                /** @var $config xPDOObject[] */
                $row = $config[$key]->toArray();
                unset($config[$key]);
                $config[$key] = $row;
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
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/jquery/jquery.min.js');
        }

        if (!empty($opts['cropper'])) {
            $controller->addCss($this->config['assetsUrl'] . 'vendor/cropper/dist/cropper.css');
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/cropper/dist/cropper.js');
        }

        if (!empty($opts['dropzone'])) {
            $controller->addLastJavascript($this->config['assetsUrl'] . 'vendor/dropzone/dist/dropzone.js');
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
        return in_array($resource->get('template'), $this->explodeAndClean($this->getOption('working_templates')));
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

    //

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


    /**
     * @param string $name
     * @param array  $properties
     *
     * @return mixed|string
     */
    public function getChunk($name = '', array $properties = array())
    {
        if (class_exists('pdoTools') AND $pdo = $this->modx->getService('pdoTools')) {
            $output = $pdo->getChunk($name, $properties);
        } elseif (strpos($name, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $name);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);
            $output = $chunk->process($properties, $content);
        } else {
            $output = $this->modx->getChunk($name, $properties);
        }

        return $output;
    }

    public function getFileMimeType($filename, $name = '')
    {
        if (function_exists('finfo_open') AND function_exists('finfo_file') AND function_exists('finfo_close')) {
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileinfo, $filename);
            finfo_close($fileinfo);

            if (!empty($mimeType)) {
                return $mimeType;
            }
        }

        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($name);

            if (!empty($mimeType)) {
                return $mimeType;
            }
        }

        $mimeTypes = array(
            'ai'      => 'application/postscript',
            'aif'     => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'asc'     => 'text/plain',
            'asf'     => 'video/x-ms-asf',
            'asx'     => 'video/x-ms-asf',
            'au'      => 'audio/basic',
            'avi'     => 'video/x-msvideo',
            'bcpio'   => 'application/x-bcpio',
            'bin'     => 'application/octet-stream',
            'bmp'     => 'image/bmp',
            'bz2'     => 'application/x-bzip2',
            'cdf'     => 'application/x-netcdf',
            'chrt'    => 'application/x-kchart',
            'class'   => 'application/octet-stream',
            'cpio'    => 'application/x-cpio',
            'cpt'     => 'application/mac-compactpro',
            'csh'     => 'application/x-csh',
            'css'     => 'text/css',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'djv'     => 'image/vnd.djvu',
            'djvu'    => 'image/vnd.djvu',
            'dll'     => 'application/octet-stream',
            'dms'     => 'application/octet-stream',
            'doc'     => 'application/msword',
            'dvi'     => 'application/x-dvi',
            'dxr'     => 'application/x-director',
            'eps'     => 'application/postscript',
            'etx'     => 'text/x-setext',
            'exe'     => 'application/octet-stream',
            'ez'      => 'application/andrew-inset',
            'flv'     => 'video/x-flv',
            'gif'     => 'image/gif',
            'gtar'    => 'application/x-gtar',
            'gz'      => 'application/x-gzip',
            'hdf'     => 'application/x-hdf',
            'hqx'     => 'application/mac-binhex40',
            'htm'     => 'text/html',
            'html'    => 'text/html',
            'ice'     => 'x-conference/x-cooltalk',
            'ief'     => 'image/ief',
            'iges'    => 'model/iges',
            'igs'     => 'model/iges',
            'img'     => 'application/octet-stream',
            'iso'     => 'application/octet-stream',
            'jad'     => 'text/vnd.sun.j2me.app-descriptor',
            'jar'     => 'application/x-java-archive',
            'jnlp'    => 'application/x-java-jnlp-file',
            'jpe'     => 'image/jpeg',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'js'      => 'application/x-javascript',
            'kar'     => 'audio/midi',
            'kil'     => 'application/x-killustrator',
            'kpr'     => 'application/x-kpresenter',
            'kpt'     => 'application/x-kpresenter',
            'ksp'     => 'application/x-kspread',
            'kwd'     => 'application/x-kword',
            'kwt'     => 'application/x-kword',
            'latex'   => 'application/x-latex',
            'lha'     => 'application/octet-stream',
            'lzh'     => 'application/octet-stream',
            'm3u'     => 'audio/x-mpegurl',
            'man'     => 'application/x-troff-man',
            'me'      => 'application/x-troff-me',
            'mesh'    => 'model/mesh',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'mif'     => 'application/vnd.mif',
            'mov'     => 'video/quicktime',
            'movie'   => 'video/x-sgi-movie',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'mpe'     => 'video/mpeg',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'mpga'    => 'audio/mpeg',
            'ms'      => 'application/x-troff-ms',
            'msh'     => 'model/mesh',
            'mxu'     => 'video/vnd.mpegurl',
            'nc'      => 'application/x-netcdf',
            'odb'     => 'application/vnd.oasis.opendocument.database',
            'odc'     => 'application/vnd.oasis.opendocument.chart',
            'odf'     => 'application/vnd.oasis.opendocument.formula',
            'odg'     => 'application/vnd.oasis.opendocument.graphics',
            'odi'     => 'application/vnd.oasis.opendocument.image',
            'odm'     => 'application/vnd.oasis.opendocument.text-master',
            'odp'     => 'application/vnd.oasis.opendocument.presentation',
            'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
            'odt'     => 'application/vnd.oasis.opendocument.text',
            'ogg'     => 'application/ogg',
            'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
            'oth'     => 'application/vnd.oasis.opendocument.text-web',
            'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
            'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'ott'     => 'application/vnd.oasis.opendocument.text-template',
            'pbm'     => 'image/x-portable-bitmap',
            'pdb'     => 'chemical/x-pdb',
            'pdf'     => 'application/pdf',
            'pgm'     => 'image/x-portable-graymap',
            'pgn'     => 'application/x-chess-pgn',
            'png'     => 'image/png',
            'pnm'     => 'image/x-portable-anymap',
            'ppm'     => 'image/x-portable-pixmap',
            'ppt'     => 'application/vnd.ms-powerpoint',
            'ps'      => 'application/postscript',
            'qt'      => 'video/quicktime',
            'ra'      => 'audio/x-realaudio',
            'ram'     => 'audio/x-pn-realaudio',
            'ras'     => 'image/x-cmu-raster',
            'rgb'     => 'image/x-rgb',
            'rm'      => 'audio/x-pn-realaudio',
            'roff'    => 'application/x-troff',
            'rpm'     => 'application/x-rpm',
            'rtf'     => 'text/rtf',
            'rtx'     => 'text/richtext',
            'sgm'     => 'text/sgml',
            'sgml'    => 'text/sgml',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'silo'    => 'model/mesh',
            'sis'     => 'application/vnd.symbian.install',
            'sit'     => 'application/x-stuffit',
            'skd'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'skp'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'snd'     => 'audio/basic',
            'so'      => 'application/octet-stream',
            'spl'     => 'application/x-futuresplash',
            'src'     => 'application/x-wais-source',
            'stc'     => 'application/vnd.sun.xml.calc.template',
            'std'     => 'application/vnd.sun.xml.draw.template',
            'sti'     => 'application/vnd.sun.xml.impress.template',
            'stw'     => 'application/vnd.sun.xml.writer.template',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'swf'     => 'application/x-shockwave-flash',
            'sxc'     => 'application/vnd.sun.xml.calc',
            'sxd'     => 'application/vnd.sun.xml.draw',
            'sxg'     => 'application/vnd.sun.xml.writer.global',
            'sxi'     => 'application/vnd.sun.xml.impress',
            'sxm'     => 'application/vnd.sun.xml.math',
            'sxw'     => 'application/vnd.sun.xml.writer',
            't'       => 'application/x-troff',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texi'    => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'tgz'     => 'application/x-gzip',
            'tif'     => 'image/tiff',
            'tiff'    => 'image/tiff',
            'torrent' => 'application/x-bittorrent',
            'tr'      => 'application/x-troff',
            'tsv'     => 'text/tab-separated-values',
            'txt'     => 'text/plain',
            'ustar'   => 'application/x-ustar',
            'vcd'     => 'application/x-cdlink',
            'vrml'    => 'model/vrml',
            'wav'     => 'audio/x-wav',
            'wax'     => 'audio/x-ms-wax',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wm'      => 'video/x-ms-wm',
            'wma'     => 'audio/x-ms-wma',
            'wml'     => 'text/vnd.wap.wml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'wmv'     => 'video/x-ms-wmv',
            'wmx'     => 'video/x-ms-wmx',
            'wrl'     => 'model/vrml',
            'wvx'     => 'video/x-ms-wvx',
            'xbm'     => 'image/x-xbitmap',
            'xht'     => 'application/xhtml+xml',
            'xhtml'   => 'application/xhtml+xml',
            'xls'     => 'application/vnd.ms-excel',
            'xml'     => 'text/xml',
            'xpm'     => 'image/x-xpixmap',
            'xsl'     => 'text/xml',
            'xwd'     => 'image/x-xwindowdump',
            'xyz'     => 'chemical/x-xyz',
            'zip'     => 'application/zip'
        );

        $ext = strtolower(array_pop(explode('.', $name)));

        if (!empty($mimeTypes[$ext])) {

            return $mimeTypes[$ext];
        }

        return 'application/octet-stream';
    }

    public function getFileMimeTypeFromBinary($binary)
    {
        if (
        !preg_match(
            '/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/',
            $binary, $hits
        )
        ) {
            return 'application/octet-stream';
        }
        static $type = array(
            1 => 'image/jpeg',
            2 => 'image/gif',
            3 => 'image/png',
            4 => 'image/x-windows-bmp',
            5 => 'image/tiff',
            6 => 'image/x-ilbm',
        );

        $mimeType = $type[count($hits) - 1];

        return !empty($mimeType) ? $mimeType : 'application/octet-stream';
    }
}