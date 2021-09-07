<?php
declare(strict_types = 1);

namespace LMS\Routes\Middleware\Api;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use LMS\Facade\Extbase\{User, ExtensionHelper, TypoScriptConfiguration};

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
abstract class AbstractRouteMiddleware
{
    use ExtensionHelper;
    use \LMS\Facade\Model\Property\User;

    private ServerRequestInterface $request;
    private array $properties;

    public function __construct(ServerRequestInterface $request, array $properties)
    {
        $this->request = $request;
        $this->properties = $properties;

        $this->setUser(User::currentUid());
    }


    /**
     * @throws MethodNotAllowedException
     */
    abstract public function process(): void;

    /**
     * @throws MethodNotAllowedException
     */
    protected function deny(string $message, int $status = 200): void
    {
        throw new MethodNotAllowedException([], $message, $status);
    }

    protected function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    protected function getProperties(): array
    {
        return $this->properties;
    }

    public function getOriginalParams(): array
    {
        return (array)$this->getRequest()->getAttributes()['_originalGetParameters'];
    }

    public function getQuery(): array
    {
        return $this->getRequest()->getQueryParams();
    }

    protected function getAdminExtensionName(): string
    {
        $extKey = (string)array_last($this->getProperties());

        return $extKey ?: self::extensionTypoScriptKey();
    }

    protected function getSettings(string $extKey): array
    {
        return TypoScriptConfiguration::getSettings($extKey) ?: [];
    }
}
