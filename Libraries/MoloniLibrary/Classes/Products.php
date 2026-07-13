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

class Products
{

    private $moloni;
    private $store = [];

    /**
     * Customers constructor.
     *
     * @param Moloni $moloni
     */
    public function __construct(Moloni $moloni)
    {
        $this->moloni = $moloni;
    }

    /**
     * @param $companyId int|bool
     *
     * @return bool|array
     */
    public function getAll($companyId = false)
    {
        $values = ["company_id" => ($companyId ?: $this->moloni->session->companyId)];
        $result = $this->moloni->execute("products/getAll", $values);
        if (is_array($result) && !isset($result['error'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Erro ao aceder aos artigos"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }

    /**
     * @param $values array
     * @param $companyId int|false
     *
     * @return array
     * @throws JsonException
     */
    public function getModifiedSinceAll(array $values, $companyId = false): array
    {
        if (!isset($values['lastmodified'])) {
            return [];
        }

        $results = [];
        $loopCounter = 0;
        $resultsCounter = 50;

        while ($resultsCounter > 0) {
            $values['offset'] = count($results);
            $products = $this->getModifiedSince($values, $companyId);
            $results[] = $products;
            $resultsCounter = count($products);
            $loopCounter++;

            // 1500 Products
            if ($loopCounter > 30) {
                break;
            }
        }

        return array_merge(...$results);
    }

    /**
     * @param $values array
     * @param $companyId int|bool
     *
     * @return array|false
     */
    public function getModifiedSince(array $values, $companyId = false)
    {
        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("products/getModifiedSince", $values);
        if (is_array($result) && !isset($result['error'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Erro ao aceder aos artigos"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return [];
    }

    /**
     * @param $values array
     * @param $companyId int|bool
     *
     * @return bool|mixed
     */
    public function getByReference(array $values, $companyId = false)
    {

        if (!isset($values['reference'])) {
            return false;
        }

        if (isset($this->store[__FUNCTION__][$values['reference']])) {
            return $this->store[__FUNCTION__][$values['reference']];
        }

        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $values['exact'] = true;
        $result = $this->moloni->execute("products/getByReference", $values);

        if (is_array($result) && isset($result[0]['product_id'])) {
            $this->store[__FUNCTION__][$values['reference']] = $result;
            return $result;
        }

        if (empty($result)) {
            return false;
        }

        $this->moloni->errors->throwError(
            __("Erro ao aceder aos artigos"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }

    /**
     * @param array $values https://www.moloni.pt/dev/index.php?action=getApiDocDetail&id=194
     * @param int|bool $companyId int|bool
     *
     * @return bool|array
     */
    public function insert(array $values, $companyId = false)
    {
        $this->store = [];
        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("products/insert", $values);

        if (is_array($result) && isset($result['product_id'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Erro ao inserir o artigo: " . $values['reference']),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );

        return false;
    }

    /**
     * @param array $values https://www.moloni.pt/dev/index.php?action=getApiDocDetail&id=195
     * @param int|bool $companyId int|bool
     *
     * @return bool|array
     */
    public function update(array $values, $companyId = false)
    {
        $this->store = [];
        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("products/update", $values);

        if (is_array($result) && isset($result['product_id'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Houve um erro ao actualziar o cliente"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }
}
