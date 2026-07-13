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

namespace Invoicing\Moloni\Cron;

use Exception;
use Invoicing\Moloni\Libraries\MoloniLibrary\Controllers\ProductsFactory as MoloniProductsFactory;
use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use Invoicing\Moloni\Logger\SyncLogger;

class ProductsSync
{

    /**
     * @var SyncLogger
     */
    protected $logger;

    /**
     * @var Moloni
     */
    protected $moloni;

    /**
     * @var MoloniProductsFactory
     */
    private $productsFactory;


    public function __construct(
        SyncLogger $logger,
        Moloni $moloni,
        MoloniProductsFactory $productsFactory
    )
    {
        $this->logger = $logger;
        $this->moloni = $moloni;
        $this->productsFactory = $productsFactory;
    }

    /**
     * Write to system.log
     *
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        if ($this->moloni->checkActiveSession()) {
            $currentDate = gmdate('Y-m-d H:i:s');
            $updateSince = $this->moloni->settings['cron_date'];
            if (!$updateSince || !strtotime($updateSince)) {
                $updateSince = $currentDate;
            }

            $this->logger->info("Updating products since " . $updateSince);

            if ($this->moloni->settings['products_sync_stock'] || $this->moloni->settings['products_sync_price']) {
                $changedMoloniProducts = $this->moloni->products->getModifiedSinceAll(['lastmodified' => $updateSince]);
                if (!empty($changedMoloniProducts) && is_array($changedMoloniProducts)) {
                    $this->logger->info("Found " . count($changedMoloniProducts) . " products do sync");
                    foreach ($changedMoloniProducts as $moloniProduct) {
                        $syncProduct = $this->productsFactory->create();

                        if ($syncProduct->syncProductFromMoloni($moloniProduct)) {
                            $this->logger->info(
                                $moloniProduct['reference'] . ' ' .
                                __("Artigo atualizado com successo")
                            );
                        } else {
                            $message = $this->moloni->errors->getErrors('first');
                            $this->logger->info(
                                $moloniProduct['reference'] . ' ' .
                                __('Erro ao actualizar o artigo ') .
                                ($message && $message['title'] ? $message['title'] : '')
                            );
                        }
                    }
                }
                $this->logger->info("Finish updating products");
            } else {
                $this->logger->info("Sync functions disabled");
            }

            $companyId = $this->moloni->session->companyId;
            $this->moloni->settingsRepository->saveSetting($companyId, "cron_date", $currentDate);
        }
    }
}
