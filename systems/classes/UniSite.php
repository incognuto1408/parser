<?php

include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/fn/query.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/fn/main.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/fn/tpl.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/libs/mail.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/libs/resize.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/libs/PHPExcel/PHPExcel.php");

include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Cashed.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Admin.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/CurrencyBoard.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Geo.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Access.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Ads.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Blog.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Banners.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/CategoryBlog.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/CategoryBoard.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Filters.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Pages.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Permission.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Profile.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Seo.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Shop.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/Subscription.php");

include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/libs/SxGeo/SxGeo.php");
$SxGeo = new SxGeo("{$_SERVER['DOCUMENT_ROOT']}/systems/libs/SxGeo/SxGeoCity.dat", SXGEO_BATCH | SXGEO_MEMORY);

include_once("{$_SERVER['DOCUMENT_ROOT']}/admin/lang/lang.php");

$settings = settings();
$settings_tpl = settings_tpl();

?>