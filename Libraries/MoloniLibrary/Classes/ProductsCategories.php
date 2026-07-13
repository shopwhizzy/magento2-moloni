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

namespace Invoicing\Moloni\Libraries\MoloniLibrary\Classes;

use Invoicing\Moloni\Libraries\MoloniLibrary\Moloni;
use JsonException;

class ProductsCategories
{

    private $moloni;
    private $store = [];

    /**
     * Customers constructor.
     * @param Moloni $moloni
     */
    public function __construct(Moloni $moloni)
    {
        $this->moloni = $moloni;
    }

    /**
     * @param array $values https://www.moloni.pt/dev/index.php?action=getApiDocDetail&id=204
     * @param int|bool $companyId
     * @return bool|mixed
     */
    public function getAll(array $values, $companyId = false)
    {

        if (!isset($values['parent_id'])) {
            $values['parent_id'] = 0;
        }

        if (isset($values['parent_id'], $this->store[$values['parent_id']])) {
            return $this->store[$values['parent_id']];
        }

        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("productCategories/getAll", $values);

        $this->store[$values['parent_id']] = $result;

        if (is_array($result) && isset($result[0]['category_id'])) {
            return $result;
        }

        if (empty($result)) {
            // No error but empty result
            return false;
        }

        $this->moloni->errors->throwError(
            __("Erro ao obter todas as categorias"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }

    /**
     * @param array $values https://www.moloni.pt/dev/index.php?action=getApiDocDetail&id=204
     * @param int|bool $companyId
     * @return bool|array
     */
    public function insert(array $values, $companyId = false)
    {
        if (!isset($values['parent_id'])) {
            $values['parent_id'] = 0;
        }

        unset($this->store[$values['parent_id']]);

        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("productCategories/insert", $values);

        if (is_array($result) && isset($result['category_id'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Houve um erro ao inserir a categoria"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }
}
