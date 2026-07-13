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
use Invoicing\Moloni\Model\TokensRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;


abstract class Home extends Action
{

    public const ADMIN_RESOURCE = 'Invoicing_Moloni::home';

    protected $moloni;
    protected $tokensRepository;
    protected $dataPersistor;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var PageFactory
     */
    protected $resultFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DataPersistor $dataPersistor,
        TokensRepository $tokensRepository,
        RedirectFactory $redirectFactory,
        Moloni $moloni
    )
    {
        parent::__construct($context);

        $this->moloni = $moloni;
        $this->resultFactory = $resultPageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->dataPersistor = $dataPersistor;
        $this->tokensRepository = $tokensRepository;

        $this->context = $context;
        $this->response = $context->getResponse();
        $this->redirect = $context->getRedirect();
        $this->request = $context->getRequest();
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return Redirect|Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultFactory->create();
        $resultPage->setActiveMenu('Invoicing_Moloni::home');
        $resultPage->addBreadcrumb(__('Moloni'), __('Moloni'));
        $resultPage->getConfig()->getTitle()->prepend(__("Moloni"));

        if (!$this->context->getAuth()->isLoggedIn()) {
            $adminUrl = $this->context->getUrl()->getUrl('admin');
            return $this->redirectFactory->create()->setUrl($adminUrl);
        }

        return $resultPage;
    }
}
