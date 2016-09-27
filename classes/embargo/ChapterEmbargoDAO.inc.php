<?php

/**
 * @file classes/embargo/ChapterEmbargoDAO.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2000-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ChapterEmbargoDAO
 * @ingroup embargo
 * @see ChapterEmbargo
 *
 * @brief Operations for retrieving and modifying ChapterEmbargo objects.
 *
 */

import('classes.monograph.Chapter');
import('classes.embargo.Embargo');

class ChapterEmbargoDAO extends DAO {
	/**
	 * Constructor
	 */
	function ChapterEmbargoDAO() {
		parent::DAO();
	}

	/**
	 * Get months of embargo for a given chapter.
	 * @param $chapterId int
	 * @return months (int). 0 if no embargo period.
	 */
	function getEmbargoMonths($chapterId) {
		$params = array();
		if (isset($chapterId)) $params[] = (int) $chapterId;

		$result = $this->retrieve(
				'SELECT	embargo_months
				FROM	submission_chapter_embargoes
				WHERE	chapter_id = ?',
				$params
		);

		if (!$result->EOF and array_key_exists('embargo_months', $result->fields)) {
			return $result->fields['embargo_months'];
		}
		return 0;
	}

	/**
	 * Get embargo date for a given chapter.
	 * @param $chapterId int
	 * @return date (Y-m-d). Null if no embargo set.
	 */
	function getEmbargoDate($chapterId) {
		$params = array((int) $chapterId);

		$result = $this->retrieve(
			'SELECT embargo_date
			FROM	submission_chapter_embargoes
			WHERE 	chapter_id = ?',
			$params
		);

		if (!$result->EOF) {
			return $result->fields['embargo_date'];
		}
		return null;
	}

	/*
	 * Has an embargo been set for this chapter (can have passed already)
	 * @return bool
	 */
	function chapterHasEmbargo($chapterId) {
		return (!is_null(getEmbargoDate($chapterid)) or getEmbargoMonths($chapterId) > 0);
	}

	/*
	 * Is the chapter under embargo
	 * @return bool
	 */
	function chapterIsUnderEmbargo($chapterId) {
		$chapterEmbargoDate = $this->getEmbargoDate($chapterId);
		if (!is_null($chapterEmbargoDate)) {
			return (Core::getCurrentDate() < $chapterEmbargoDate);
		}
		return false;
	}
	/*
	 * Gets a ChapterEmbargo object for a given chapterId
	 * @param $chapterId int
	 */
	function getObject($chapterId) {
		$result = $this->retrieve(
			'SELECT	*
			FROM	submission_chapter_embargoes
			WHERE	chapter_id = ?',
			array((int) $chapterId)
		);
		if (!$result->EOF) {
			return $this->_fromRow($result->GetRowAssoc(false));
		}
		return null;
	}

	/*
	 * Inserts a new chapter embargo into table
	 * @param $chapterEmbargo Embargo
	 */
	function insertObject($chapterEmbargo) {
		$this->update(
			sprintf('INSERT INTO submission_chapter_embargoes
				(chapter_id, embargo_months, embargo_date)
				VALUES
				(?, ?, %s)',
				$this->datetimetoDB($chapterEmbargo->getEmbargoDate())),
			array(
				(int) $chapterEmbargo->getAssociatedId(),
				(int) $chapterEmbargo->getEmbargoMonths(),
			)
		);
	}

	/*
	 * Updates a chapter embargo
	 * @param $chapterEmbargo Embargo
	 */
	function updateObject($chapterEmbargo) {
		$this->update(
			sprintf('UPDATE	submission_chapter_embargoes
				SET embargo_months = ?,
					embargo_date = %s
				WHERE	chapter_id = ?',
				$this->datetimetoDB($chapterEmbargo->getEmbargoDate())),
			array(
				(int) $chapterEmbargo->getEmbargoMonths(),
				(int) $chapterEmbargo->getAssociatedId(),
			)
		);
	}

	/**
	 * Construct and return a new data object.
	 * @return ChapterEmbargo
	 */
	function newDataObject() {
		return new Embargo();
	}

	/**
	 * Internal function to return an ChapterEmbargo object from a row.
	 * @param $row array
	 * @return ChapterEmbargo
	 */
	function _fromRow($row) {
		$chapterEmbargo = new Embargo();
		
		$chapterEmbargo->setAssociatedId($row['chapter_id']);
		$chapterEmbargo->setEmbargoMonths($row['embargo_months']);
		$chapterEmbargo->setEmbargoDate($row['embargo_date']);
		
		return $chapterEmbargo;
	}
}

?>
