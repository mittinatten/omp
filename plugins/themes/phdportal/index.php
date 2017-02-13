<?php

/**
 * @defgroup plugins_themes_phdportal PhDPortal theme plugin
 */

/**
 * @file plugins/themes/phdportal/index.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_themes_phdportal
 * @brief Wrapper for phdportal theme plugin.
 *
 */

require_once('PhDPortalThemePlugin.inc.php');

return new PhDPortalThemePlugin();

?>
