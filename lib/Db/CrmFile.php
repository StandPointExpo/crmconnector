<?php

namespace OCA\CrmConnector\Db;

use OCA\CrmConnector\Exception\FileExtException;
use OCP\AppFramework\Db\Entity;

/**
 * @method void setId(int $id)
 * @method int getId()
 * @method void setUserId(int $userID)
 * @method int getUserId()
 * @method void setUuid(string $uuid)
 * @method int getUuid()
 * @method void setPublication(bool $publication)
 * @method bool isPublication()
 * @method void setFileOriginalName(string $fileOriginalName)
 * @method string getFileOriginalName()
 * @method void setFileType(string $fileType)
 * @method string getFileType()
 * @method void setFileSource(string $fileSource)
 * @method string getFileSource()
 * @method void setFileShare(string $fileShare)
 * @method string getFileShare()
 * @method void setExtension(string $extension)
 * @method string getExtension()
 * @method void setDeletedAt(mixed $deletedAt)
 * @method mixed getDeletedAt()
 * @method void setCreatedAt(mixed $createdAt)
 * @method mixed getCreatedAt()
 * @method void setUpdatedAt(mixed $updatedAt)
 * @method mixed getUpdatedAt()
 */
class CrmFile extends Entity
{
    /** @var int */
    protected $userId;

    /** @var string */
    protected $uuid;

    /** @var bool */
    protected $publication;

    /** @var string */
    protected $fileOriginalName;

    /** @var string */
    protected $fileType;

    /** @var string */
    protected $fileSource;

    /** @var string */
    protected $fileShare;

    /** @var string */
    protected $extension;

    /** @var mixed */
    protected $deletedAt;

    /** @var mixed */
    protected $createdAt;

    /** @var mixed */
    protected $updatedAt;

    public const IMAGE_EXT = ['JPG', 'jpg', 'jpeg', 'png', 'gif', 'tiff'];

    public const DOCUMENT_EXT = [
        'doc',
        'docx',
        'dot',
        'odt',
        'xlt',
        'dwg',
        'xls',
        'xml',
        'xlsx',
        'xlsm',
        'xltm',
        'txt',
        'ods',
        'docm',
        'dotx',
        'dotm',
        'wpd',
        'wps',
        'csv',
        'ppt',
        'pps',
        'pot',
        'pptx',
        'pptm',
        'potx',
        'potm',
        'sxw',
        'stw',
        'sxc',
        'stc',
        'xlw',
        'eps',
        'tif',
        'xsd',
        'dwg',
        'ai',
        'tiff',
        'eps',
        'ai',
        'tif',
        'psd',
        'svg',
        //Archives
        '7z',
        'zip',
        'ace',
        'arj',
        'cab',
        'cbr',
        'gz',
        'gzip',
        'pkg',
        'sit',
        'spl',
        'tar',
        'tar-gz',
        'tgz',
        'xar',
        'zipx',
        'rar',
        'rpm'
    ];

    const PROJECT_DOCUMENT_EXT = ['doc', 'docx', 'pdf', 'odt', 'zip', 'cdr', 'CDR'];
    const PROJECT_AUDIO_EXT = ['mp3', 'ogg', 'mpga'];
    const PROJECT_VIDEO_EXT = ['mp4', 'mpeg'];

    const CRM_USER = 'admin';
    const CRM_STORAGE = 'projects';


    public function __construct()
    {
        $this->addType('userId', 'integer');
        $this->addType('uuid', 'string');
        $this->addType('publication', 'bool');
        $this->addType('fileOriginalName', 'string');
        $this->addType('fileType', 'string');
        $this->addType('fileSource', 'string');
        $this->addType('fileShare', 'string');
        $this->addType('extension', 'string');
        $this->addType('deletedAt', 'string');
        $this->addType('createdAt', 'string');
        $this->addType('updatedAt', 'string');
    }

    public function validExtensions()
    {
        return array_merge(
            self::IMAGE_EXT,
            self::DOCUMENT_EXT,
            self::PROJECT_DOCUMENT_EXT,
            self::PROJECT_AUDIO_EXT,
            self::PROJECT_VIDEO_EXT
        );
    }

    public function validTypes(): array
    {
        return array(
            'ai'      => 'application/postscript',
            'aif'     => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'asc'     => 'text/plain',
            'atom'    => 'application/atom+xml',
            'au'      => 'audio/basic',
            'avi'     => 'video/x-msvideo',
            'bcpio'   => 'application/x-bcpio',
            'bin'     => 'application/octet-stream',
            'bmp'     => 'image/bmp',
            'cdf'     => 'application/x-netcdf',
            'cgm'     => 'image/cgm',
            'class'   => 'application/octet-stream',
            'cpio'    => 'application/x-cpio',
            'cpt'     => 'application/mac-compactpro',
            'csh'     => 'application/x-csh',
            'css'     => 'text/css',
            'csv'     => 'text/csv',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'djv'     => 'image/vnd.djvu',
            'djvu'    => 'image/vnd.djvu',
            'dll'     => 'application/octet-stream',
            'dmg'     => 'application/octet-stream',
            'dms'     => 'application/octet-stream',
            'doc'     => 'application/msword',
            'dtd'     => 'application/xml-dtd',
            'dvi'     => 'application/x-dvi',
            'dxr'     => 'application/x-director',
            'eps'     => 'application/postscript',
            'etx'     => 'text/x-setext',
            'exe'     => 'application/octet-stream',
            'ez'      => 'application/andrew-inset',
            'gif'     => 'image/gif',
            'gram'    => 'application/srgs',
            'grxml'   => 'application/srgs+xml',
            'gtar'    => 'application/x-gtar',
            'hdf'     => 'application/x-hdf',
            'hqx'     => 'application/mac-binhex40',
            'htm'     => 'text/html',
            'html'    => 'text/html',
            'ice'     => 'x-conference/x-cooltalk',
            'ico'     => 'image/x-icon',
            'ics'     => 'text/calendar',
            'ief'     => 'image/ief',
            'ifb'     => 'text/calendar',
            'iges'    => 'model/iges',
            'igs'     => 'model/iges',
            'jpe'     => 'image/jpeg',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'js'      => 'application/x-javascript',
            'json'    => 'application/json',
            'kar'     => 'audio/midi',
            'latex'   => 'application/x-latex',
            'lha'     => 'application/octet-stream',
            'lzh'     => 'application/octet-stream',
            'm3u'     => 'audio/x-mpegurl',
            'man'     => 'application/x-troff-man',
            'mathml'  => 'application/mathml+xml',
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
            'oda'     => 'application/oda',
            'ogg'     => 'application/ogg',
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
            'ra'      => 'audio/x-pn-realaudio',
            'ram'     => 'audio/x-pn-realaudio',
            'ras'     => 'image/x-cmu-raster',
            'rdf'     => 'application/rdf+xml',
            'rgb'     => 'image/x-rgb',
            'rm'      => 'application/vnd.rn-realmedia',
            'roff'    => 'application/x-troff',
            'rss'     => 'application/rss+xml',
            'rtf'     => 'text/rtf',
            'rtx'     => 'text/richtext',
            'sgm'     => 'text/sgml',
            'sgml'    => 'text/sgml',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'silo'    => 'model/mesh',
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
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'svg'     => 'image/svg+xml',
            'svgz'    => 'image/svg+xml',
            'swf'     => 'application/x-shockwave-flash',
            't'       => 'application/x-troff',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texi'    => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'tif'     => 'image/tiff',
            'tiff'    => 'image/tiff',
            'tr'      => 'application/x-troff',
            'tsv'     => 'text/tab-separated-values',
            'txt'     => 'text/plain',
            'ustar'   => 'application/x-ustar',
            'vcd'     => 'application/x-cdlink',
            'vrml'    => 'model/vrml',
            'vxml'    => 'application/voicexml+xml',
            'wav'     => 'audio/x-wav',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wml'     => 'text/vnd.wap.wml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'wrl'     => 'model/vrml',
            'xbm'     => 'image/x-xbitmap',
            'xht'     => 'application/xhtml+xml',
            'xhtml'   => 'application/xhtml+xml',
            'xls'     => 'application/vnd.ms-excel',
            'xml'     => 'application/xml',
            'xpm'     => 'image/x-xpixmap',
            'xsl'     => 'application/xml',
            'xslt'    => 'application/xslt+xml',
            'xul'     => 'application/vnd.mozilla.xul+xml',
            'xwd'     => 'image/x-xwindowdump',
            'xyz'     => 'chemical/x-xyz',
            'zip'     => 'application/zip',
            'cdr'     => 'application/coreldraw',
        );
    }

    /**
     * @param $resumableFilename
     * @return string
     */
    public function getExt($resumableFilename): ?string
    {
        return pathinfo($resumableFilename, PATHINFO_EXTENSION);
    }

    /**
     * @param $resumableFilename
     * @return string
     */
    public function getType($resumableFilename): ?string
    {
        $ext = self::getExt($resumableFilename);

        if (in_array($ext, self::IMAGE_EXT)) {
            return 'image';
        }

        if (in_array($ext, self::PROJECT_AUDIO_EXT)) {
            return 'audio';
        }

        if (in_array($ext, self::PROJECT_VIDEO_EXT)) {
            return 'video';
        }

        if (in_array($ext, self::PROJECT_DOCUMENT_EXT)) {
            return 'document';
        }

        return 'file';
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'uuid' => $this->getUuid(),
            'publication' => $this->isPublication(),
            'file_original_name' => $this->getFileOriginalName(),
            'file_type' => $this->getFileType(),
            'file_source' => $this->getFileSource(),
            'file_share' => $this->getFileShare(),
            'extension' => $this->getExtension(),
            'deleted_at' => $this->getDeletedAt(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }


}