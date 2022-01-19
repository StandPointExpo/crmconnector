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
            'application/postscript',
            'audio/x-aiff',
            'audio/x-aiff',
            'audio/x-aiff',
            'text/plain',
            'video/x-msvideo',
            'image/bmp',
            'image/x-ms-bmp',
            'image/dib',
            'application/x-netcdf',
            'image/x-jpf',
            'image/x-jp2',
            'image/cgm',
            'text/csv',
            'application/x-director',
            'application/x-director',
            'image/vnd.djvu',
            'image/vnd.djvu',
            'application/msword',
            'application/xml-dtd',
            'application/x-dvi',
            'application/x-director',
            'text/x-setext',
            'image/gif',
            'image/x-icon',
            'text/calendar',
            'image/ief',
            'image/jpeg',
            'image/jpg',
            'image/jpe',
            'image/vnd.ms-photo',
            'application/json',
            'audio/midi',
            'video/quicktime',
            'video/x-sgi-movie',
            'audio/mpeg',
            'video/mpeg',
            'video/vnd.mpegurl',
            'application/x-netcdf',
            'application/oda',
            'application/ogg',
            'image/x-portable-bitmap',
            'chemical/x-pdb',
            'application/pdf',
            'image/x-portable-graymap',
            'application/x-chess-pgn',
            'image/png',
            'image/x-portable-anymap',
            'image/x-portable-pixmap',
            'application/vnd.ms-powerpoint',
            'video/quicktime',
            'image/x-cmu-raster',
            'application/rdf+xml',
            'image/x-rgb',
            'application/smil',
            'image/svg+xml',
            'application/x-tar',
            'image/tif',
            'image/tiff',
            'application/x-troff',
            'text/plain',
            'image/vnd.wap.wbmp',
            'application/vnd.ms-excel',
            'application/xml',
            'image/x-xpixmap',
            'application/xml',
            'application/xslt+xml',
            'application/vnd.mozilla.xul+xml',
            'image/x-xwindowdump',
            'application/zip',
            'application/coreldraw',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.text-template',
            'application/vnd.oasis.opendocument.text-web',
            'application/vnd.oasis.opendocument.text-master',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.graphics-template',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.presentation-template',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.spreadsheet-template',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.formula',
            'application/vnd.oasis.opendocument.database',
            'application/vnd.oasis.opendocument.image',
            'application/vnd.openofficeorg.extension',
            'application/vnd.sun.xml.writer',
            'application/vnd.sun.xml.writer.template',
            'application/vnd.sun.xml.calc',
            'application/vnd.sun.xml.calc.template',
            'application/vnd.sun.xml.draw',
            'application/vnd.sun.xml.draw.template',
            'application/vnd.sun.xml.impress',
            'application/vnd.sun.xml.impress.template',
            'application/vnd.sun.xml.writer.global',
            'application/vnd.sun.xml.math',
            'application/eps',
            'application/x-eps',
            'image/eps',
            'image/x-eps',
            'image/x-dwg',
            'image/x-dxf',
            'drawing/x-dwf'
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