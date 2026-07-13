<?php
/**
 * Module for Magento 2 by Moloni
 * Copyright (C) 2026  Moloni, lda
 *
 * This file is part of Invoicing/Moloni.
 *
 * Invoicing/Moloni is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Supporting the latest Adobe Commerce 2.4.7, 2.4.8 and 2.4.9 versions
 *
 * @link    https://shopwhizzy.com
 * @author  info@shopwhizzy.com
 */

namespace Invoicing\Moloni\Controller\Adminhtml;

use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;


/**
 * Class Settings
 *
 * @package Invoicing\Moloni\Controller\Adminhtml
 */
abstract class Settings extends Action
{

    public const ADMIN_RESOURCE = 'Invoicing_Moloni::settings';
    protected $moloni;
    protected $messageManager;
    protected $context;
    protected $resultFactory;

    /**
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * @var RedirectInterface
     */
    protected $redirectInterface;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Settings constructor.
     *
     * @param $context Context
     * @param $resultPageFactory PageFactory
     * @param RedirectFactory $redirectFactory
     * @param $messageManager ManagerInterface
     * @param $Moloni Moloni
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Moloni $Moloni
    )
    {
        parent::__construct($context);

        $this->context = $context;
        $this->resultFactory = $resultPageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->moloni = $Moloni;

        $this->response = $context->getResponse();
        $this->requestInterface = $context->getRequest();
        $this->redirectInterface = $context->getRedirect();
    }

    /**
     * @return Redirect|Page
     */
    protected function initAction()
    {
        if (!$this->context->getAuth()->isLoggedIn()) {
            $adminUrl = $this->context->getUrl()->getUrl('admin');
            return $this->redirectFactory->create()->setPath($adminUrl);
        }

        $resultPage = $this->resultFactory->create();
        $resultPage->setActiveMenu('Invoicing_Moloni::settings');
        $resultPage->addBreadcrumb(__('Moloni'), __('Moloni'));
        $resultPage->getConfig()->getTitle()->prepend(__("Configurações"));
        return $resultPage;
    }
}
