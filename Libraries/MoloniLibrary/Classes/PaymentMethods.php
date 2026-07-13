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

class PaymentMethods
{

    private $moloni;
    private $store = [];

    /**
     * Payment Methods constructor.
     * @param Moloni $moloni
     */
    public function __construct(Moloni $moloni)
    {
        $this->moloni = $moloni;
    }

    /**
     * @param bool|int $company_id
     * @return bool|mixed
     */
    public function getAll($company_id = false)
    {
        if (isset($this->store[__FUNCTION__])) {
            return $this->store[__FUNCTION__];
        }

        $values = ["company_id" => ($company_id ?: $this->moloni->session->companyId)];
        $result = $this->moloni->execute("paymentMethods/getAll", $values);
        $this->store[__FUNCTION__] = $result;
        if (is_array($result) && isset($result[0]['payment_method_id'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Não tem acesso à informação das dos métodos de pagamento"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }

    /**
     * @param array $values [code => payment method code; name => payment method name]
     * @param bool|int $companyId
     * @return bool|array
     */
    public function insert(array $values, $companyId = false)
    {
        $this->store = [];
        $values['company_id'] = ($companyId ?: $this->moloni->session->companyId);
        $result = $this->moloni->execute("paymentMethods/insert", $values);

        if (is_array($result) && isset($result['payment_method_id'])) {
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Houve um erro ao inserir o método de pagamento"),
            __(json_encode($result, JSON_PRETTY_PRINT)),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }
}
