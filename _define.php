<?php
/**
 * @brief mrvbPagination, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Mirovinben (http://www.mirovinben.fr/)
 *
 * @copyright GPL-2.0 [https://www.gnu.org/licenses/gpl-2.0.html]
 */

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'mrvbPagination',           					// Name
    'Advanced Pagination Links', 					// Description
    'Fix + Topaz + Mirovinben',   					// Authors
    '1.4',                   						// Version
    [
        'requires' => [['core', '2.24']],   		// Dependencies
        'permissions' => 'usage,contentadmin', 		// Permissions
        'type' => 'plugin',             	    	// Type
        'support' => 'https://www.mirovinben.fr/blog/index.php?post/id1562',
        'details' => 'https://plugins.dotaddict.org/dc2/details/mrvbPagination'
    ]
);
