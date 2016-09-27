<?php

/**
 * @file classes/embargo/SubmissionEmbargoDAO.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2000-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionEmbargoDAO
 * @ingroup embargo
 * @see SubmissionEmbargo
 *
 * @brief Operations for retrieving and modifying ChapterEmbargo objects.
 *
 */

import('classes.monograph.Monograph');
import('classes.embargo.Embargo');

class SubmissionEmbargoDAO extends DAO {
	/**
	 * Constructor
	 */
	function SubmissionEmbargoDAO() {
		parent::DAO();
	}

	/**
	 * Get months of embargo for a given chapter.
	 * @param $chapterId int
	 * @return months (int). 0 if no embargo period.
	 */
	function getEmbargoMonths($submissionId) {
		$params = array();
		$params[] = (int) $submissionId;

		$result = $this->retrieve(
				'SELECT	embargo_months
				FROM	submission_embargoes
				WHERE	submission_id = ?',
				$params
		);

		if (!$result->EOF and array_key_exists('embargo_months', $result->fields)) {
			return $result->fields['embargo_months'];
		}
		return 0;
	}

	/**
	 * Get embargo date for a given submission.
	 * @param $submissionId int
	 * @return date (Y-m-d). Null if no embargo set.
	 */
	function getEmbargoDate($submissionId) {
		$result = $this->retrieve(
			'SELECT embargo_date
			FROM	submission_embargoes
			WHERE 	submission_id = ?',
			array((int) $submissionId)
		);

		if (!$result->EOF) {
			return $result->fields['embargo_date'];
		}
		return null;
	}

	/*
	 * Has an embargo been set for this submission (can have passed already)
	 * @return bool
	 */
	function submissionHasEmbargo($submissionId) {
		return (!is_null(getEmbargoDate($submissionid)) or getEmbargoMonths($submissionId) > 0);
	}

	/*
	 * Is the submission under embargo
	 * @return bool
	 */
	function submissionIsUnderEmbargo($submissionId) {
		$embargoDate = $this->getEmbargoDate($submissionId);
		if (!is_null($embargoDate)) {
			return (Core::getCurrentDate() < $embargoDate);
		}
		return false;
	}

	/*
	 * Gets a SubmissionEmbargo object for a given submissionId
	 * @param $submissionId int
	 */
	function getObject($submissionId) {
		$result = $this->retrieve(
			'SELECT	*
			FROM	submission_embargoes
			WHERE	submission_id = ?',
			array((int) $submissionId)
		);
		if (!$result->EOF) {
			return $this->_fromRow($result->GetRowAssoc(false));
		}
		return null;
	}

	/*
	 * Inserts a new submission embargo into table
	 * @param $submissionEmbargo SubmissionEmbargo
	 */
	function insertObject($submissionEmbargo) {
		$this->update(
			sprintf('INSERT INTO submission_embargoes
				(submission_id, embargo_months, embargo_date)
				VALUES
				(?, ?, %s)',
				$this->datetimetoDB($submissionEmbargo->getEmbargoDate())),
			array(
				(int) $submissionEmbargo->getAssociatedId(),
				(int) $submissionEmbargo->getEmbargoMonths(),
			)
		);
	}

	/*
	 * Updates a submission embargo
	 * @param $submissionEmbargo SubmissionEmbargo
	 */
	function updateObject($submissionEmbargo) {
		$this->update(
			sprintf('UPDATE	submission_embargoes
				SET embargo_months = ?,
					embargo_date = %s
				WHERE	submission_id = ?',
				$this->datetimetoDB($submissionEmbargo->getEmbargoDate())),
			array(
				(int) $submissionEmbargo->getEmbargoMonths(),
				(int) $submissionEmbargo->getAssociatedId(),
			)
		);
	}

	/**
	 * Construct and return a new data object.
	 * @return SubmissionEmbargo
	 */
	function newDataObject() {
		return new Embargo();
	}

	/**
	 * Internal function to return an SubmissionEmbargo object from a row.
	 * @param $row array
	 * @return SubmissionEmbargo
	 */
	function _fromRow($row) {
		$submissionEmbargo = new Embargo();
		
		$submissionEmbargo->setAssociatedId($row['submission_id']);
		$submissionEmbargo->setEmbargoMonths($row['embargo_months']);
		$submissionEmbargo->setEmbargoDate($row['embargo_date']);
		
		return $submissionEmbargo;
	}
}

?>
