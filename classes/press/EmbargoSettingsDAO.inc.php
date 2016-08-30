<?php

/**
 * @file classes/press/EmbargoSettingsDAO.inc.php
 *
 * Copyright (c) 2016 Simon Mitternacht
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class EmbargoSettingsDAO
 * @ingroup press
 *
 * @brief Class for the available embargo times
 */

import('lib.pkp.classes.core.DataObject');

class EmbargoSettingsDAO extends DAO {

	function EmbargoSettingsDAO() {
		parent::DAO();
	}
	
	function addOption($embargoMonths) {
		if (!$this->hasOption($embargoMonths)) {
			$this->update(
				'INSERT INTO embargo_settings (allowed_embargo_months) VALUES (?)',
				array((int) $embargoMonths)
			);
		}
		
	}

	function deleteOption($embargoMonths) {
		$this->update(
			'DELETE FROM embargo_settings WHERE allowed_embargo_months = ?',
			array((int) $embargoMonths)
		);
	}

	function hasOption($embargoMonths) {
		$result = $this->retrieve(
			'SELECT * FROM embargo_settings WHERE allowed_embargo_months = ?',
			array($embargoMonths));
		if ($result->RecordCount() > 0) return true;
		return false;
	}
	
	function getOptions() {
		$result = $this->retrieve('SELECT allowed_embargo_months FROM embargo_settings');
		$rows = $result->GetArray();
		$options = array();
		foreach ($rows as $row) {
			$options[] = $row['allowed_embargo_months'];
		}
		return $options;
	}
	
}

?>
