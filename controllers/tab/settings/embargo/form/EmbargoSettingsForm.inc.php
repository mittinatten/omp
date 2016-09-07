<?php

/**
 * @file controllers/tab/settings/embargo/form/EmbargoSettingsForm.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class EmbargoSettingsForm
 * @ingroup controllers_tab_settings_embargo_form
 *
 * @brief Form to edit content embargo settings.
 */

import('lib.pkp.classes.controllers.tab.settings.form.ContextSettingsForm');

class EmbargoSettingsForm extends ContextSettingsForm {

	/**
	 * Constructor.
	 */
	function EmbargoSettingsForm($settings = array(), $wizardMode = false) {
		parent::ContextSettingsForm(
			array(
				'enableEmbargo' => 'bool',
				'embargoPeriods' => 'object',
				'allowPermanentEmbargo' => 'bool',
				'permanentEmbargoPeriod' => 'int',
			),
			'controllers/tab/settings/embargo/form/embargoSettingsForm.tpl',
			$wizardMode
		);
	}

}

?>
