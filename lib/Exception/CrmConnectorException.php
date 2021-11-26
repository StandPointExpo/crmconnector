<?php

namespace OCA\CrmConnector\Exception;

abstract class CrmConnectorException extends \InvalidArgumentException {

    const REASON = 'File error';
    const CODE  = 422;

    public function __construct()
    {
        parent::__construct(static::REASON, static::CODE);
    }
}