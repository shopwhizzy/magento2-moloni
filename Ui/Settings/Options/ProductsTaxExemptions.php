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

class ProductsTaxExemptions implements OptionSourceInterface
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

        $exemptions = $this->moloni->taxExemptions->getAll();

        $result[] = [
            'value' => '',
            'label' => __('Sem razão de isenção seleccionada')
        ];

        if ($exemptions && is_array($exemptions)) {
            foreach ($exemptions as $exemption) {
                if (!is_array($exemption) || !isset($exemption['code'], $exemption['name'])) {
                    continue;
                }

                $result[] = [
                    "value" => $exemption['code'],
                    "label" => $exemption['name']
                ];
            }
        }

        return $result;
    }
}
