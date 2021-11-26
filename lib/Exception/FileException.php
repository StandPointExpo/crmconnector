<?php

namespace OCA\CrmConnector\Exception;

class FileException extends CrmConnectorException
{
    const REASON = "File data incorrect";
    const CODE = 422;
}