<?php

namespace OCA\CrmConnector\Controller;

use OCA\CrmConnector\Db\CrmToken;
use OCP\AppFramework\PublicShareController;

class CrmUserController extends PublicShareController
{
    /**
     * Return the hash of the password for this share.
     * This function is of course only called when isPasswordProtected is true
     */
    protected function getPasswordHash(): string {
        return md5('secretpassword');
    }

    /**
     * Validate the token of this share. If the token is invalid this controller
     * will return a 404.
     */
    public function isValidToken(): bool {
        return $this->getToken() === CrmToken::APP_TOKEN;
    }

    /**
     * Allows you to specify if this share is password protected
     */
    protected function isPasswordProtected(): bool {
        return false;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     *
     * @param string $title
     * @param string $content
     */
    public function note($title, $content) {
        // Work your magic
        return 'sdsdsds';
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     *
     * @param string $title
     * @param string $content
     */
    public function index($title, $content) {
        // Work your magic
        return 'sdsdsds';
    }
}