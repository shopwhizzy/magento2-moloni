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

namespace Invoicing\Moloni\Controller\Adminhtml\Sync;

use Invoicing\Moloni\Libraries\MoloniLibrary\Controllers\ProductsFactory as MoloniProductsFactory;
use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;

class Product extends Action
{

    public const ADMIN_RESOURCE = 'Invoicing_Moloni::home';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Moloni
     */
    protected $moloni;

    /**
     * @var PageFactory
     */
    protected $resultFactory;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var MoloniProductsFactory
     */
    private $productsFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * @var RedirectInterface
     */
    protected $redirectInterface;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var HttpInterface
     */
    protected $http;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Moloni $moloni
     * @param MoloniProductsFactory $productsFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param PageFactory $resultFactory
     */
    public function __construct(
        Context $context,
        Moloni $moloni,
        MoloniProductsFactory $productsFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        PageFactory $resultFactory
    )
    {
        parent::__construct($context);

        $this->moloni = $moloni;
        $this->context = $context;
        $this->productsFactory = $productsFactory;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;

        $this->response = $context->getResponse();
        $this->requestInterface = $context->getRequest();
        $this->redirectInterface = $context->getRedirect();
    }


    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        if (!$this->moloni->checkActiveSession()) {
            $this->messageManager->addErrorMessage(__('Erro com sessão Moloni'));

            return $this->redirectFactory->create()->setPath('catalog/product/index');
        }

        $productId = $this->requestInterface->getParam('id');

        if (!$productId) {
            $this->messageManager->addErrorMessage(__('Id de artigo não encontrado'));
            return $this->redirectFactory->create()->setPath('catalog/product/index');
        }

        $syncProduct = $this->productsFactory->create();

        if ($syncProduct->syncProductFromId($productId)) {
            if ($syncProduct->productInserted) {
                $this->messageManager->addSuccessMessage(__("Artigo inserido no Moloni com successo"));
            } else {
                $this->messageManager->addSuccessMessage(__("Artigo atualizado com successo"));
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('Erro ao actualizar o artigo: ') .
                $this->moloni->errors->getErrors('first')['message']
            );
        }

        return $this->redirectFactory->create()->setPath('catalog/product/index', ['id' => $productId]);
    }
}
