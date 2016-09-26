<?php

/**
 * @file classes/embargo/SubmissionEmbargo.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionEmbargo
 * @ingroup Embargo
 *
 * @brief Submission embargo
 *
 * @defgroup Embargo Interface to set embargoes for documents
 */

class SubmissionEmbargo extends DataObject {

	/**
	 * Constructor.
	 */
	function SubmissionEmbargo() {
		parent::DataObject();
	}

	//
	// Get/set methods
	//

	/*
	 * Get Submission ID.
	 * @return int
	 */
	function getSubmissionId() {
		return $this->getData('submissionId');
	}

	/*
	 * Set Submission ID.
	 * @param $monographId int
	 */
	function setSubmissionId($submissionId) {
		return $this->setData('submissionId', $submissionId);
	}

	/*
	 * Get embargo period.
	 * @return int
	 */
	function getEmbargoMonths() {
		return $this->getData('embargoMonths');
	}

	/*
	 * Set embargo period.
	 * @param $embargoMonths int
	 */
	function setEmbargoMonths($embargoMonths) {
		return $this->setData('embargoMonths', $embargoMonths);
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

	/*
	 * Get embargo date as current-date plus embargo months
	 * @return string (date, Y-m-d). Null if no embargo months set.
	 */
	function calculateEmbargoDate() {
		$embargoMonths = $this->getEmbargoMonths();
		if ($embargoMonths > 0) {
			$date = new DateTime(Core::getCurrentDate());
			$date->add(new DateInterval('P' . $embargoMonths . 'M'));
			return $date->format('Y-m-d');
		}
		return null;
	}
}

?>
