<?php
/**
* @package   yoo_moreno
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get html head data
$head = $this->getHeadData();

// remove deprecated meta-data (html5)
unset($head['metaTags']['http-equiv']);
unset($head['metaTags']['standard']['title']);
unset($head['metaTags']['standard']['rights']);
unset($head['metaTags']['standard']['language']);

$this->setHeadData($head);

?>
<!DOCTYPE HTML>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
	<meta charset="<?php echo $this->getCharset(); ?>">
	<jdoc:include type="head" />
    <link rel="stylesheet" href="/templates/yoo_moreno/styles/DeVosTemplate/css/theme.css">
    <link rel="stylesheet" href="/templates/yoo_moreno/css/custom.css">
</head>
<body class="contentpane">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>