<?php

declare(strict_types=1);

/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Meta\Sales\Plugin;

use Magento\Framework\Exception\FileSystemException;
use Magento\Config\Model\Config;

class ShippingSettingsUpdatePlugin
{
    /**
     * @var ShippingSyncer
     */
    private ShippingSyncer $shippingSyncer;

    /**
     * @param ShippingSyncer $shippingSyncer
     */
    public function __construct(
        ShippingSyncer $shippingSyncer
    ) {
        $this->shippingSyncer = $shippingSyncer;
    }

    /**
     * This function is called whenever shipping settings are saved in Magento
     *
     * @param Config $config
     */
    public function afterSave(Config $config): void
    {
        $sectionName = $config->getSection();
        if ($sectionName !== 'carriers') {
            return;
        }
        $this->shippingSyncer->syncShippingProfiles('after_save');
    }
}
