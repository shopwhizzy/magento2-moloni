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

namespace Invoicing\Moloni\Block\Documents;

use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use JsonException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class DocumentsList extends Template
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Moloni
     */
    protected $moloni;

    /**
     * DocumentsList constructor.
     * @param Context $context
     * @param Moloni $moloni
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Moloni $moloni,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->moloni = $moloni;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        $orderId = $this->getRequest()->getParam("order_id");
        return $this->orderRepository->get($orderId);
    }

    /**
     * @return array
     *
     * @throws JsonException
     */
    public function getOrderDocuments(): array
    {
        $documentsList = [];
        $incrementId = $this->getOrder()->getIncrementId();
        if (!empty($incrementId) && $this->moloni->checkActiveSession()) {
            $moloniDocuments = $this->moloni->documents->setDocumentType('documents');
            $documentsList = $moloniDocuments->getAll(['status' => 1, 'your_reference' => $incrementId]);
            if ($documentsList && is_array($documentsList)) {
                foreach ($documentsList as &$document) {
                    if ((int)$document['status'] === 1) {
                        $currentDocumentType = $moloniDocuments->setDocumentType($document['document_type_id']);
                        $documentDownloadUrl = $currentDocumentType->getDownloadUrl(
                            ['document_id' => $document['document_id']]
                        );
                        $document['document_type_name'] = $currentDocumentType->documentTypeName;

                        if ($documentDownloadUrl) {
                            $document['download_url'] = $documentDownloadUrl;
                        }
                    }
                }
            }
        }

        return $documentsList ?: [];
    }
}
