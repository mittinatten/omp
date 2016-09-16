<?php

/**
 * @file classes/monograph/ChapterEmbargo.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ChapterEmbargo
 * @ingroup monograph
 * @see ChapterEmbargoDAO
 *
 * @brief Adds embargoes to chapters
 *
 * If feature is enabled authors can set embargo periods for
 * individual chapters. At publication the embargo periods should be
 * recalculated to dates.
 */

class ChapterEmbargo extends DataObject {

	/**
	 * Constructor.
	 */
	function ChapterEmbargo() {
		parent::DataObject();
	}

	//
	// Get/set methods
	//

	/**
	 * Get Chapter ID of this chapter
	 * @return int
	 */
	function getChapterId() {
		return $this->getData('chapterId');
	}

	/**
	 * Set ID of chapter.
	 * @param $chapterId int
	 */
	function setChapterId($chapterId) {
		return $this->setData('chapterId', $chapterId);
	}

	/*
	 * Get Monograph ID of chapter.
	 * @return int
	 */
	function getMonographId() {
		return $this->getData('monographId');
	}

	/*
	 * Set Monograph ID of chapter.
	 * @param $monographId int
	 */
	function setMonographId($monographId) {
		return $this->setData('monographId', $monographId);
	}

	/*
	 * Get embargo period for chapter.
	 * @return int
	 */
	function getEmbargoMonths() {
		return $this->getData('embargoMonths');
	}

	/*
	 * Set embargo period for chapter.
	 * @param $embargoMonths int
	 */
	function setEmbargoMonths($embargoMonths) {
		return $this->setData('embargoMonths', $embargoMonths);
	}

	/*
	 * Get embargo date for chapter.
	 * @return date (string)
	 */
	function getEmbargoUntil() {
		return $this->getData('embargoDate');
	}

	/*
	 * Set embargo date for chapter.
	 * @param $date string
	 */
	function setEmbargoUntil($date) {
		return $this->setData('embargoDate', $date);
	}
}

?>
