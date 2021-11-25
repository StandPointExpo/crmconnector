<?php

namespace OCA\CrmConnector\Migration;

use OCA\CrmConnector\Controller\FileReceive;
use OCA\CrmConnector\Service\CrmFileService;
use OCP\IConfig;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface;

class SeedsStep implements IRepairStep
{
    private const FILE_NAME = 'files.json';

    /** @var LoggerInterface */
    protected $logger;
    /**
     * @var IConfig
     */
    private $config;
    /**
     * @var FileReceive
     */
    private $fileReceive;
    /**
     * @var mixed
     */
    private $files;
    /**
     * @var CrmFileService
     */
    private $crmFileService;

    public function __construct(LoggerInterface $logger, IConfig $config, FileReceive $fileReceive, CrmFileService $crmFileService)
    {
        $this->config = $config;
        $this->fileReceive = $fileReceive;
        $this->logger = $logger;
        $this->files = json_decode('./feeds/' . self::FILE_NAME, true);
        $this->crmFileService = $crmFileService;
    }

    /**
     * Returns the step's name
     */
    public function getName()
    {
        return 'A demonstration repair step!';
    }

    /**
     * @param IOutput $output
     */
    public function run(IOutput $output)
    {
        try {
            $this->fileReceive->_log('var_dump($file)');
            $this->crmFileService->addFile([
                'id' => 1,
                'user_id' => 2,
                'uuid' => 'jvjxck-jvlkxcj-vjx-clj',
                'publication' => true,
                'file_original_name' => 'file_original_name',
                'file_type' => 'file_type',
                'file_source' => 'file_source',
                'file_share' => 'file_share',
                'extension' => 'extension',
                'deleted_at' => '2021-11-02 14:46:52',
                'created_at' => '2021-11-02 14:46:52',
                'updated_at' => '2021-11-02 14:46:52'
            ]);
            $this->fileReceive->_log('var_dump($file)');
//            foreach ($this->files as $file) {
//                $this->logger->warning("Hello world from MyRepairStep!", $file);
//                die();
//            }
            $this->logger->warning("Hello world from MyRepairStep!", ["app" => "MyApp"]);
        } catch (\Throwable $exception) {
            $this->fileReceive->_log($exception->getMessage());
        }
    }
}
