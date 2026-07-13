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

class Companies
{

    private $moloni;
    private $store = [];

    /**
     * Companies constructor.
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
    public function getOne($company_id = false)
    {
        if (isset($this->store[__FUNCTION__])) {
            return $this->store[__FUNCTION__];
        }

        $values = ["company_id" => ($company_id ?: $this->moloni->session->companyId)];
        $result = $this->moloni->execute("companies/getOne", $values);
        if (is_array($result) && isset($result['company_id'])) {
            $this->store[__FUNCTION__] = $result;
            return $result;
        }

        $this->moloni->errors->throwError(
            __("Não tem acesso à informação da empresa"),
            __("Não tem acesso à informação da empresa."),
            __CLASS__ . "/" . __FUNCTION__
        );
        return false;
    }

    /**
     * @return array|bool
     */
    public function getAll()
    {
        $result = $this->moloni->execute("companies/getAll", null);
        if (is_array($result) && isset($result[0]['company_id'])) {
            foreach ($result as $key => $company) {
                // Unset company_id 5 (Empresa de Demonstração) due to lack of privleges
                if ((int)$company['company_id'] === 5) {
                    unset($result[$key]);
                }
            }
            return $result;
        }

        return $this->moloni->errors->throwError(
            __("Não tem empresas disponíveis"),
            __("Não tem empresas disponíveis para serem usadas"),
            __CLASS__ . "/" . __FUNCTION__
        );
    }
}
