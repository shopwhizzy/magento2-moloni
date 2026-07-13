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

use Invoicing\Moloni\Libraries\MoloniLibrary\Controllers\DocumentsFactory as MoloniDocumentsFactory;
use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use Invoicing\Moloni\Model\DocumentsRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;


abstract class Documents extends Action
{

    public const ADMIN_RESOURCE = 'Invoicing_Moloni::documents';

    /**
     * @var Moloni
     */
    protected $moloni;

    /**
     * @var MoloniDocumentsFactory
     */
    protected $moloniDocumentsFactory;

    /**
     * @var DocumentsRepository
     */
    protected $documentsRepository;

    /**
     * @var PageFactory
     */
    protected $resultFactory;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;


    /**
     * Documents constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Moloni $moloni
     * @param MoloniDocumentsFactory $moloniDocumentsFactory
     * @param DocumentsRepository $documentsRepository
     * @param UrlInterface $urlBuilder
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Moloni $moloni,
        MoloniDocumentsFactory $moloniDocumentsFactory,
        DocumentsRepository $documentsRepository,
        UrlInterface $urlBuilder,
        RedirectFactory $redirectFactory
    )
    {
        parent::__construct($context);

        $this->context = $context;
        $this->moloni = $moloni;
        $this->moloniDocumentsFactory = $moloniDocumentsFactory;
        $this->documentsRepository = $documentsRepository;
        $this->resultFactory = $resultPageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->urlBuilder = $urlBuilder;

        $this->redirect = $context->getRedirect();
        $this->request = $context->getRequest();
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return ResultInterface
     */
    protected function initAction(): ResultInterface
    {
        $resultPage = $this->resultFactory->create();

        if (!$this->context->getAuth()->isLoggedIn()) {
            $adminUrl = $this->context->getUrl()->getUrl('admin');
            return $this->redirectFactory->create()->setUrl($adminUrl);
        }

        $resultPage->setActiveMenu('Invoicing_Moloni::home');
        $resultPage->addBreadcrumb(__('Moloni'), __('Moloni'));
        $resultPage->getConfig()->getTitle()->prepend(__("Moloni"));
        return $resultPage;
    }

    /**
     * @param int $orderId
     * @return bool
     */
    protected function documentExists(int $orderId): bool
    {
        $forceDocumentCreation = (int)$this->context->getRequest()->getParam('force') === 1;
        $hasDocument = $this->documentsRepository->getByOrderId($orderId);
        if ($hasDocument && !$forceDocumentCreation) {
            $forceCreateUrlParams = ['order_id' => $orderId, 'force' => true];
            $forceCreateUrl = $this->urlBuilder->getUrl('moloni/documents/create', $forceCreateUrlParams);

            $this->context->getMessageManager()->addComplexErrorMessage(
                'createDocumentExistsMessage',
                [
                    'invoice_date' => $hasDocument[0]->getInvoiceDate(),
                    'create_url' => $forceCreateUrl,
                ]
            );

            return true;
        }

        return false;
    }
}
