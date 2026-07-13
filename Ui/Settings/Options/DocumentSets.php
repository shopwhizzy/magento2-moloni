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
use Magento\Framework\Data\OptionSourceInterface;

class DocumentSets implements OptionSourceInterface
{
    private $moloni;

    /**
     * DocumentSets constructor.
     *
     * @param $moloni Moloni
     */
    public function __construct(Moloni $moloni)
    {
        $this->moloni = $moloni;
    }

    /**
     * Retrieve options array.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $result = [];

        $documentSets = $this->moloni->documentSets->getAll();

        $result[] = [
            'value' => '',
            'label' => __('Escolher uma opção')
        ];

        if ($documentSets && is_array($documentSets)) {
            foreach ($documentSets as $documentSet) {
                if (!is_array($documentSet) || !isset($documentSet['document_set_id'], $documentSet['name'])) {
                    continue;
                }

                if (!$documentSet['is_invisible']) {
                    $result[] = [
                        "value" => $documentSet['document_set_id'],
                        "label" => $documentSet['name']
                    ];
                }
            }
        }

        return $result;
    }
}
