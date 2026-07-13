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

namespace Invoicing\Moloni\Libraries\MoloniLibrary\Dependencies;

class ApiErrors
{
    private $error_log = [];

    public function hasError(): bool
    {
        return !empty($this->error_log);
    }

    public function throwError($title, $message, $where, $received = false, $sent = false): bool
    {
        if (is_array($message)) {
            foreach ($message as $msg) {
                $this->logError($title, $msg, $where, $received, $sent);
            }
        } else {
            $this->logError($title, $message, $where, $received, $sent);
        }

        return false;
    }

    public function getErrors($order = "all")
    {
        if ($this->error_log && is_array($this->error_log)) {
            switch ($order) {
                case "first":
                    return $this->error_log[0];
                case "last":
                    return end($this->error_log);
                case "all":
                default:
                    return $this->error_log;
            }
        } else {
            return false;
        }
    }

    public function clearErrors(): void
    {
        $this->error_log = [];
    }

    private function logError($title, $message, $where, $received = false, $sent = false): void
    {
        $this->error_log[] = [
            "title" => $title,
            "message" => $this->translateMessage($message),
            "where" => $where,
            "values" => [
                "received" => $received,
                "sent" => $sent
            ]
        ];
    }

    private function translateMessage($string): string
    {
        switch ($string) {
            case "1 name":
                $string = "Campo nome não pode estar em branco";
                break;
            case "1 number":
                $string = "Campo number não pode estar em branco";
                break;
            case "2 maturity_date_id 1 0":
                $string = "Defina um prazo de vencimento nas configurações do plugin";
                break;
            case "2 unit_id 1 0":
                $string = "Unidade de medida errada";
                break;
            case "1 exemption_reason":
                $string = "Um dos artigos requer uma razão de isenção";
                break;
            case "5 exemption_reason":
                $string = "Um dos artigos não tem uma razão de isenção definida";
                break;
            case "5 document_set_id":
                $string = "Não está definida a série onde quer emitir o documento";
                break;
            case "2 price 0 null null 0":
                $string = "Um dos artigos tem o preço igual a 0";
                break;

            case "2 category_id 1 0":
                $string = "Um dos artigos não tem uma categoria definida.";
                break;
        }

        if (strpos($string, '1 exemption_reason')) {
            $string = "Um dos artigos requer uma razão de isenção";
        }

        return $string;
    }
}
