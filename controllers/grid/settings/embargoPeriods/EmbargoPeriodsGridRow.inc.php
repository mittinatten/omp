<?php
/**
 * @file controllers/grid/settings/series/SeriesGridRow.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SeriesGridRow
 * @ingroup controllers_grid_settings_series
 *
 * @brief Handle series grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class EmbargoPeriodsGridRow extends GridRow {
	/**
	 * Constructor
	 */
	function EmbargoPeriodsGridRow() {
		parent::GridRow();
	}
	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

		$this->setupTemplate($request);

		// Is this a new embargo period row or an existing row?
		$embargoPeriodId = $this->getId();
		if (is_numeric($embargoPeriodId)) {
			$router = $request->getRouter();

			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editEmbargoPeriod',
					new AjaxModal(
						$router->url($request, null, null, 'editEmbargoPeriod', null, array('embargoPeriodId' => $embargoPeriodId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);

			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'deleteEmbargoPeriod',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'deleteEmbargoPeriod', null, array('embargoPeriodId' => $embargoPeriodId)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				)
			);
		}
	}
	
	/**
	 * @see PKPHandler::setupTemplate()
	 */
	function setupTemplate($request) {
		// Load manager translations. FIXME are these needed?
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_MANAGER,
			LOCALE_COMPONENT_PKP_COMMON,
			LOCALE_COMPONENT_PKP_USER,
			LOCALE_COMPONENT_APP_COMMON
		);
	}


};
?>