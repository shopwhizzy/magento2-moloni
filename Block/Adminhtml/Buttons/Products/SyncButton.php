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

namespace Invoicing\Moloni\Block\Adminhtml\Buttons\Products;

use Magento\Catalog\Block\Adminhtml\Product\Edit;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SyncButton
 */
class SyncButton extends Edit implements ButtonProviderInterface
{
    /**
     * Clear Cache button
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $message = __('As alterações efectuadas serão perdidas, deseja continuar?');
        $syncUrl = $this->getUrl('moloni/sync/product', ['id' => $this->getProductId()]);

        return [
            'id' => 'moloni_sync_product',
            'label' => __('Sincronizar Moloni'),
            'on_click' => "confirmSetLocation('$message', '$syncUrl')",
            'class' => 'delete',
            'sort_order' => 0
        ];
    }
}
