<?php

/**
 * @file controllers/grid/settings/embargoPeriods/form/EmbargoPeriodsForm.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class EmbargoPeriodsForm
 * @ingroup controllers_grid_settings_embargo_periods
 *
 * @brief Form for adding/edditing an EmbargoPeriod
 * stores/retrieves from an associative array
 */

import('lib.pkp.classes.form.Form');

class EmbargoPeriodsForm extends Form {
	/** @var int The id for the EmbargoPeriod being edited**/
	var $embargoPeriodId;
	var $embargoPeriod;
	
	/**
	 * Constructor
	 */
	function EmbargoPeriodsForm($embargoPeriodId = null, $embargoPeriod = null) {
		$this->embargoPeriodId = $embargoPeriodId;
		$this->embargoPeriod = $embargoPeriod;
		parent::Form('controllers/grid/settings/embargoPeriods/form/embargoPeriodsForm.tpl');
		$this->addCheck(new FormValidatorCSRF($this));
	}

	function getEmbargoPeriodId() {
		return $this->embargoPeriodId;
	}
	
	function initData($args, $request) {
		$context = $request->getContext();
		$id = $this->embargoPeriodId;

		$embargoPeriods = $context->getSetting('embargoPeriods');

		$this->_data = array( 'embargoPeriodId' => $id);

		// init form with empty string
		$this->_data['embargoPeriod']  = '';
		if (isset($id) && isset($embargoPeriods[$id])) {
			$this->_data['embargoPeriod'] = $embargoPeriods[$id];
			error_log(">> " . $this->_data['embargoPeriodId']);
		} 
		
		// grid related data
		//$this->_data['gridId'] = $args['gridId'];
		$this->_data['rowId'] = isset($args['rowId']) ? $args['rowId'] : null;
	}
	
	function readInputData() {
		$this->readUserVars(array('embargoPeriodId', 'embargoPeriod'));
		$this->readUserVars(array('gridId', 'rowId'));
	}

	function execute($args, $request) {
		$context = $request->getContext();
		$embargoPeriods = $context->getSetting('embargoPeriods');

		//FIXME: a bit of kludge to get unique embargoPeriod id's
		$this->embargoPeriodId = ($this->embargoPeriodId != null ? $this->embargoPeriodId : (max(array_keys($embargoPeriods)) + 1));

		$embargoPeriod = $this->getData('embargoPeriod');
		$embargoPeriods[$this->embargoPeriodId] = (int) $embargoPeriod;
		
		$context->updateSetting('embargoPeriods', $embargoPeriods, 'object', false);
		
		return true;
	}
}

?>
