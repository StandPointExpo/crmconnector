<?php

namespace OCA\CrmConnector\Exception;

class UserException extends CrmConnectorException
{
    const REASON = "You cannot sign with those credentials";
    const CODE = 401;
}