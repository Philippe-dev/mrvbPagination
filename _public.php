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

dcCore::app()->tpl->addValue('PaginationLinks', ['mrvbPaginationTpl', 'PaginationLinks']);

dcCore::app()->tpl->addBlock('IfFirstPage', ['mrvbPaginationTpl', 'IfFirstPage']);
dcCore::app()->tpl->addBlock('IfMoreOnePage', ['mrvbPaginationTpl', 'IfMoreOnePage']);
dcCore::app()->tpl->addBlock('IfOnlyOnePage', ['mrvbPaginationTpl', 'IfOnlyOnePage']);
dcCore::app()->tpl->addBlock('IfOtherPage', ['mrvbPaginationTpl', 'IfOtherPage']);
dcCore::app()->tpl->addBlock('IfLastPage', ['mrvbPaginationTpl', 'IfLastPage']);
dcCore::app()->tpl->addValue('LastPageRank', ['mrvbPaginationTpl', 'LastPageRank']);
dcCore::app()->tpl->addValue('NumberOfEntries', ['mrvbPaginationTpl', 'NumberOfEntries']);
dcCore::app()->tpl->addValue('PageAfterURL', ['mrvbPaginationTpl', 'PageAfterURL']);
dcCore::app()->tpl->addValue('PageBeforeURL', ['mrvbPaginationTpl', 'PageBeforeURL']);
dcCore::app()->tpl->addValue('ThisPageRank', ['mrvbPaginationTpl', 'ThisPageRank']);

class mrvbPaginationTpl
{
    public static function PaginationLinks($attr)
    {
        $j = intval($attr['jump'] ?? '7');
        $b = html::escapeHTML(isset($attr['before']) ? __($attr['before']) : '<');
        $a = html::escapeHTML(isset($attr['after']) ? __($attr['after']) : '>');
        $e = html::escapeHTML(isset($attr['etc']) ? __($attr['etc']) : '...');

        $m = html::escapeHTML(isset($attr['margin']) ? __($attr['margin']) : '');
        $l = html::escapeHTML(isset($attr['margin-left']) ? __($attr['margin-left']) : '');
        $r = html::escapeHTML(isset($attr['margin-right']) ? __($attr['margin-right']) : '');
        if (strlen($l . $r) == 0) {
            $l = $m;
            $r = $m;
        }

        $p = '<?php
		if (!function_exists(\'mrvbAffBlocPage\')) {
			function mrvbAffBlocPage($pageNumber, $linkText, $jump, $max) {
				if (isset($GLOBALS["_page_number"])) {
					$current = $GLOBALS["_page_number"];
				} else {
					$current = 1;
				}
				$rang = ($pageNumber == 1) ? \'first\' : \'other\';
				if ($pageNumber == $max) $rang = \'last\';
				if ($pageNumber != $current) {
					$args = $_SERVER["URL_REQUEST_PART"];
					$args = preg_replace("#(^|/)page/([0-9]+)$#","",$args);
					$url = $GLOBALS["core"]->blog->url.$args;
					if ($pageNumber > 1) {
						$url = preg_replace("#/$#","",$url);
						$url .= "/page/".$pageNumber;
					}
					if (!empty($_GET["q"])) {
						$s = strpos($url,"?") !== false ? "&amp;" : "?";
						$url .= $s."q=".$_GET["q"];
					}
					switch ($linkText){
					case "' . $b . '":
						$title = sprintf(__(\'Bloc of %s pages before\'),$jump);
						$rang.= \' before\';
						break;
					case "' . $a . '":
						$title = sprintf(__(\'Bloc of %s pages after\'),$jump);
						$rang.= \' after\';
						break;
					default :
						$title = __(\'Go to page\').\' \'.$linkText;
						$rang.= (($pageNumber)%2 == 0) ? \' even\' : \' odd\';
					}
					return "' . $l . '<a class=\"".$rang."\" href=\"".$url."\" title=\"".$title."\">".$linkText."</a>' . $r . "\n" . '";
				} else {
					$rang.= (($pageNumber)%2 == 0) ? \' even\' : \' odd\';
					return "' . $l . '<span class=\"this ".$rang."\" title=\"".__(\'This page\')."\">".$linkText."</span>' . $r . "\n" . '";
				}
			}
		}
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		if (dcCore::app()->ctx->exists("pagination")) {
			$nb_posts = dcCore::app()->ctx->pagination->f(0);
		}
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		$nb_max_pages = 10;
		$quick_distance = ' . $j . '; 
		
		if($nb_pages <= $nb_max_pages) {
			for ($i = 1; $i <= $nb_pages; $i++) {
				echo mrvbAffBlocPage($i,$i,$quick_distance,$nb_pages);
			}
		} else {
			echo mrvbAffBlocPage(1,1,$quick_distance,$nb_pages);
			$min_page = intval(max($current - ($quick_distance - 1) / 2, 2));
			$max_page = intval(min($current + ($quick_distance - 1) / 2, $nb_pages - 1));
			if ($min_page > 2) {
				echo "<span class=\"etc\">' . $e . '</span>' . "\n" . '";
				if($current >= $quick_distance + 1) {
					echo mrvbAffBlocPage($current - $quick_distance,"' . $b . '",$quick_distance,$nb_pages);
				}
			}
			for ($i = $min_page; $i <= $max_page ; $i++) {
				echo mrvbAffBlocPage($i,$i,$quick_distance,$nb_pages);
			}
			if ($max_page < $nb_pages - 1) {
				if($current <= $nb_pages - $quick_distance) {
					echo mrvbAffBlocPage($current + $quick_distance,"' . $a . '",$quick_distance,$nb_pages);
				}
				echo "<span class=\"etc\">' . $e . '</span>' . "\n" . '";
			}
			echo mrvbAffBlocPage($nb_pages,$nb_pages,$quick_distance,$nb_pages);
		} 
		?>';

        return $p;
    }

    public static function IfFirstPage($attr, $content)
    {
        return
        '<?php
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		if (($current == 1) && ($nb_pages > 1)){ ?>' .
        $content .
        '<?php } ?>';
    }

    public static function IfMoreOnePage($attr, $content)
    {
        return
        '<?php
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		if ($nb_pages > 1){ ?>' .
        $content .
        '<?php } ?>';
    }

    public static function IfOnlyOnePage($attr, $content)
    {
        return
        '<?php
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		if ($nb_pages == 1){ ?>' .
        $content .
        '<?php } ?>';
    }

    public static function IfOtherPage($attr, $content)
    {
        return
        '<?php
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		if (($current > 1) && ($current < $nb_pages)){ ?>' .
        $content .
        '<?php } ?>';
    }

    public static function IfLastPage($attr, $content)
    {
        return
        '<?php
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		if (($current == $nb_pages) && ($nb_pages > 1)){ ?>' .
        $content .
        '<?php } ?>';
    }

    public static function LastPageRank($attr)
    {
        return
        '<?php 
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		echo($nb_pages);
		?>';
    }

    public static function NumberOfEntries($attr)
    {
        return
        '<?php 
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		echo($nb_posts);
		?>';
    }

    public static function PageAfterURL($attr)
    {
        return
        '<?php
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$nb_posts = dcCore::app()->ctx->pagination->f(0);
		$nb_per_page = dcCore::app()->ctx->post_params["limit"][1];
		if (!isset($nb_per_page)) $nb_per_page = 1;
		$nb_pages = ceil($nb_posts/$nb_per_page);
		$args = $_SERVER["URL_REQUEST_PART"];
		$args = preg_replace("#(^|/)page/([0-9]+)$#","",$args);
		$url = $GLOBALS["core"]->blog->url.$args;
		if ($current < $nb_pages) {
			$url = preg_replace("#/$#","",$url);
			$url .= "/page/".($current + 1);
		}
		if (!empty($_GET["q"])) {
			$s = strpos($url,"?") !== false ? "&amp;" : "?";
			$url .= $s."q=".$_GET["q"];
		}
		echo($url);
		?>';
    }

    public static function PageBeforeURL($attr)
    {
        return
        '<?php
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		$args = $_SERVER["URL_REQUEST_PART"];
		$args = preg_replace("#(^|/)page/([0-9]+)$#","",$args);
		$url = $GLOBALS["core"]->blog->url.$args;
		if ($current > 2) {
			$url = preg_replace("#/$#","",$url);
			$url .= "/page/".($current - 1);
		}
		if (!empty($_GET["q"])) {
			$s = strpos($url,"?") !== false ? "&amp;" : "?";
			$url .= $s."q=".$_GET["q"];
		}
		echo($url);
		?>';
    }

    public static function ThisPageRank($attr)
    {
        return
        '<?php 
		if (dcCore::app()->public->getPageNumber() !== null) {
			$current = dcCore::app()->public->getPageNumber();
		} else {
			$current = 1;
		}
		echo($current);
		?>';
    }
}
