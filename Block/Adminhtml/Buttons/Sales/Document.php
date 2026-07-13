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

namespace Invoicing\Moloni\Block\Adminhtml\Buttons\Sales;

use Invoicing\Moloni\Api\Data\DocumentsInterface;
use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni as MoloniLibrary;
use Invoicing\Moloni\Model\DocumentsRepository;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class Document
{
    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var AuthorizationInterface */
    protected $authorization;

    /**
     * @var DocumentsRepository
     */
    private $documentsRepository;
    /**
     * @var MoloniLibrary
     */
    protected $moloni;

    public function __construct(
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        DocumentsRepository $documentsRepository,
        MoloniLibrary $moloni

    )
    {
        $this->documentsRepository = $documentsRepository;
        $this->urlBuilder = $urlBuilder;
        $this->moloni = $moloni;

        $this->authorization = $authorization;
    }

    public function beforeSetLayout(OrderView $view)
    {

        $orderId = $view->getOrderId();
        if (!$this->hasMoloniLog($orderId)) {
            $url = $this->urlBuilder->getUrl('moloni/documents/create', ['order_id' => $orderId]);

            $view->addButton(
                'moloni_create_document',
                [
                    'label' => __('Gerar documento Moloni'),
                    'sort_order' => 2,
                    'on_click' => 'window.open( \'' . $url . '\')',
                ]
            );
        }
    }

    /**
     * @param int $orderId
     * @return bool|DocumentsInterface[]|AbstractExtensibleObject[]
     */
    private function hasMoloniLog(int $orderId)
    {
        $hasDocument = $this->documentsRepository->getByOrderId($orderId);
        if (!$hasDocument) {
            return false;
        }
        return $hasDocument;
    }
}
