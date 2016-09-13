<?php
/**
 * @file controllers/grid/settings/embargoPeriods/EmbargoPeriodsGridRow.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesGridRow
 * @ingroup controllers_grid_settings_embargo_periods
 *
 * @brief Handle series grid row requests.
 */

import('lib.pkp.controllers.grid.settings.SetupGridHandler');
import('controllers.grid.settings.embargoPeriods.EmbargoPeriodsGridRow');

class EmbargoPeriodsGridHandler extends SetupGridHandler {
	/**
	 * Constructor
	 */
	function EmbargoPeriodsGridHandler() {
		parent::SetupGridHandler();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'addEmbargoPeriod', 'editEmbargoPeriod', 'updateEmbargoPeriod', 'deleteEmbargoPeriod', 'saveSequence')
		);
	}
	
	//
	// Overridden template methods
	//
	/*
	 * Configure the grid
	 * @param $request PKPRequest
	 */
	function initialize($request) {
		parent::initialize($request);
		$press = $request->getPress();
		// FIXME are these all required?
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_MANAGER,
			LOCALE_COMPONENT_PKP_COMMON,
			LOCALE_COMPONENT_PKP_USER,
			LOCALE_COMPONENT_APP_COMMON
		);

		// Set the grid title.
		$this->setTitle('catalog.manage.embargoOptions');

		// Set grid data
		$embargoPeriods = $press->getSetting('embargoPeriods');
		$gridData = array();
		if ($embargoPeriods) {
			foreach ($embargoPeriods as $i => $t) {
				$gridData[$i] = array('period' => $t);
			}
		}
		$this->setGridDataElements($gridData);

		// Add grid-level actions
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addEmbargoPeriod',
				new AjaxModal(
					$router->url($request, null, null, 'addEmbargoPeriod', null, array('gridId' => $this->getId())),
					__('grid.action.addEmbargoPeriod'),
					'modal_manage'
				),
				__('grid.action.addEmbargoPeriod')
			)
		);

		$this->addColumn(new GridColumn('period', 'period'));
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * Get the list of "publish data changed" events.
	 * Used to update the site context switcher upon create/delete.
	 * @return array
	 */
	function getPublishChangeEvents() {
		return array('updateSidebar');
	}

	/**
	 * Get the row handler - override the default row handler
	 * @return EmbargoPeriodsGridRow
	 */
	function getRowInstance() {
		return new EmbargoPeriodsGridRow();
	}

	//
	// Public Embargo Periods Grid Actions
	//
	/**
	 * An action to add a new embargo period
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function addEmbargoPeriod($args, $request) {
		return $this->editEmbargoPeriod($args, $request);
	}

	/**
	 * An action to edit an embargo period
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function editEmbargoPeriod($args, $request) {
		$embargoPeriodId = isset($args['embargoPeriodId']) ? $args['embargoPeriodId'] : null;
		$this->setupTemplate($request);
		
		import('controllers.grid.settings.embargoPeriods.form.EmbargoPeriodsForm');
		$embargoPeriodsForm = new EmbargoPeriodsForm($embargoPeriodId);
		$embargoPeriodsForm->initData($args, $request);
		return new JSONMessage(true, $embargoPeriodsForm->fetch($request));
	}

	/**
	 * Update an embargo period
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function updateEmbargoPeriod($args, $request) {
		$embargoPeriodId = $request->getUserVar('embargoPeriodId');

		import('controllers.grid.settings.embargoPeriods.form.EmbargoPeriodsForm');
		$embargoPeriodsForm = new EmbargoPeriodsForm($embargoPeriodId);
		$embargoPeriodsForm->readInputData();
		if ($embargoPeriodsForm->validate()) {
			$embargoPeriodsForm->execute($args, $request);
			return DAO::getDataChangedEvent($embargoPeriodsForm->getEmbargoPeriodId());
		} else {
			return new JSONMessage(false);
		}
	}
	
	/**
	 * Delete an embargo period
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function deleteEmbargoPeriod($args, $request) {
		$press = $request->getPress();
		$embargoPeriodId = $request->getUserVar('embargoPeriodId');
		$embargoPeriods = $press->getSetting('embargoPeriods');
		if (isset($embargoPeriods) && isset($embargoPeriods[$embargoPeriodId])) {
			unset($embargoPeriods[$embargoPeriodId]);
			$press->updateSetting('embargoPeriods', $embargoPeriods, 'object', false);
			return DAO::getDataChangedEvent($embargoPeriodId);
		} else {
			// FIXME Appropriate error message here?
			AppLocale::requireComponents(LOCALE_COMPONENT_PKP_MANAGER); // manager.setup.errorDeletingItem
			return new JSONMessage(false, __('manager.setup.errorDeletingItem'));
		}
	}
}
?>
