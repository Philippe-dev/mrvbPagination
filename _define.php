<?php
/**
 * @brief mrvbPagination, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Mirovinben (https://www.mirovinben.fr/)
 *
 * @copyright GPL-2.0 [https://www.gnu.org/licenses/gpl-2.0.html]
 */
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'mrvbPagination',
    'Advanced Pagination Links',
    'Fix + Topaz + Mirovinben',
    '1.4',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN]),
        'type'        => 'plugin',
        'support'     => 'https://www.mirovinben.fr/blog/index.php?post/id1562',
        'details'     => 'https://plugins.dotaddict.org/dc2/details/mrvbPagination',
    ]
);
