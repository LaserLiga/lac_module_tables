<?php

declare(strict_types=1);

namespace LAC\Modules\Tables\Services;

use LAC\Modules\Core\MenuExtensionInterface;

class MenuExtension implements MenuExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function extend(array &$menu): void {
        if ( ! isset($menu['settings'])) {
            return;
        }
        $menu['settings']['children'][] = [
            'name' => lang('Tables'),
            'route' => 'settings-tables',
        ];
    }
}
