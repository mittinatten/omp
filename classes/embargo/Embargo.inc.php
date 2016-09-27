<?php

/**
 * @file classes/embargo/Embargo.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class Embargo
 * @ingroup Embargo
 *
 * @brief Embargo for a document
 *
 * @defgroup Embargo Interface to set embargoes for documents
 */

class Embargo extends DataObject {

	/**
	 * Constructor.
	 */
	function Embargo() {
		parent::DataObject();
	}

	//
	// Get/set methods
	//

	/*
	 * Get associated ID. Submission or chapter.
	 * @return int
	 */
	function getAssociatedId() {
		return $this->getData('id');
	}

	/*
	 * Set associated ID. Submission or chapter.
	 * @param $id int
	 */
	function setAssociatedId($id) {
		return $this->setData('id', $id);
	}

	/*
	 * Get embargo date.
	 * @return string (date)
	 */
	function getEmbargoDate() {
		return $this->getData('embargoDate');
	}

	/*
	 * Set embargo date.
	 * @param $date string
	 */
	function setEmbargoDate($date) {
		return $this->setData('embargoDate', $date);
	}

}

?>
