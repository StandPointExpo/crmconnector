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
    public const FILE_NAME = 'files.json';

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
    private CrmFileService $crmFileService;

    public function __construct(
        LoggerInterface $logger,
        IConfig $config,
        FileReceive $fileReceive,
        CrmFileService $crmFileService)
    {
        $this->config = $config;
        $this->fileReceive = $fileReceive;
        $this->logger = $logger;
        $json = file_get_contents(__DIR__. '/feeds/' . SeedsStep::FILE_NAME);
        $this->files = json_decode($json, true);
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
            foreach ($this->files as $file) {
                $file['file_source'] = str_replace('public/repository/projects/', '', $file['file_source']);
                $this->crmFileService->create($file);
            }
            $this->logger->warning("Hello world from MyRepairStep!", ["app" => "MyApp"]);
        } catch (\Throwable $exception) {
            $this->fileReceive->_log($exception->getMessage());
        }
    }
}
