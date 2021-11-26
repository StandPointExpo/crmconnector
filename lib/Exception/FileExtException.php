<?php

namespace OCA\CrmConnector\Exception;

class FileExtException extends \InvalidArgumentException
{
    const REASON = "File data incorrect";
    const CODE = 412;

    public function __construct(string $file)
    {
        parent::__construct(sprintf('The file "%s" extension does not support', $file), self::CODE);
    }
}