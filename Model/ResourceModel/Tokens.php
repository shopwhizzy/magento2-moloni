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

namespace Invoicing\Moloni\Model\ResourceModel;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Tokens extends AbstractDb
{
    /**
     * Columns holding OAuth credentials/secrets that must never be stored in plain text.
     */
    public const ENCRYPTED_FIELDS = ['secret_token', 'access_token', 'refresh_token'];

    private EncryptorInterface $encryptor;

    public function __construct(Context $context, EncryptorInterface $encryptor)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /** @noinspection MagicMethodsValidityInspection */
    public function _construct()
    {
        $this->_init('moloni_tokens', 'id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        foreach (self::ENCRYPTED_FIELDS as $field) {
            $value = $object->getData($field);
            if ($value !== null && $value !== '' && !self::looksEncrypted((string)$value)) {
                $object->setData($field, $this->encryptor->encrypt((string)$value));
            }
        }

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        foreach (self::ENCRYPTED_FIELDS as $field) {
            $value = $object->getData($field);
            if ($value !== null && $value !== '' && self::looksEncrypted((string)$value)) {
                $object->setData($field, $this->encryptor->decrypt((string)$value));
            }
        }

        return parent::_afterLoad($object);
    }

    /**
     * Encrypt/decrypt must be idempotent: the same in-memory Tokens object can be saved more
     * than once within a request (e.g. the OAuth grant save, then a later setCompanyId() save),
     * and pre-existing rows may still hold legacy plain-text values from before encryption was
     * added. Without this check, a second save would re-encrypt already-encrypted ciphertext,
     * and loading a legacy plain-text row would run it through decrypt() and corrupt it.
     * Magento's Encryptor output always starts with "<keyVersion>:<cipherVersion>:", which a
     * real OAuth token/secret won't coincidentally match.
     */
    public static function looksEncrypted(string $value): bool
    {
        return (bool)preg_match('/^\d+:\d+:/', $value);
    }
}
