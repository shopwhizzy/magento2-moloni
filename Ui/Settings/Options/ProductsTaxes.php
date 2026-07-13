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

namespace Invoicing\Moloni\Ui\Settings\Options;

use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use JsonException;
use Magento\Framework\Data\OptionSourceInterface;

class ProductsTaxes implements OptionSourceInterface
{
    private $moloni;

    /**
     * DocumentSets constructor.
     * @param Moloni $moloni
     */
    public function __construct(Moloni $moloni)
    {
        $this->moloni = $moloni;
    }

    /**
     * Retrieve options array.
     * @return array
     * @throws JsonException
     */
    public function toOptionArray(): array
    {
        $result = [];

        $taxes = $this->moloni->taxes->getAll();

        $result[] = [
            'value' => '',
            'label' => __('Os meus artigos têm taxas configuradas')
        ];

        if ($taxes && is_array($taxes)) {
            foreach ($taxes as $tax) {
                if (!is_array($tax) || !isset($tax['tax_id'], $tax['name'], $tax['value'])) {
                    continue;
                }

                $result[] = [
                    "value" => $tax['tax_id'],
                    "label" => $tax['name'] . " (" . $tax['value'] . "%)"
                ];
            }
        }

        return $result;
    }
}
