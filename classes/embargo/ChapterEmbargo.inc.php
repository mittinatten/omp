<?php

/**
 * @file classes/embargo/ChapterEmbargo.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ChapterEmbargo
 * @ingroup embargo
 * @see ChapterEmbargoDAO
 *
 * @brief Adds embargoes to chapters
 *
 * If feature is enabled authors can set embargo periods for
 * individual chapters. At publication the embargo periods should be
 * recalculated to dates.
 */

import('classes.embargo.SubmissionEmbargo');

class ChapterEmbargo extends SubmissionEmbargo {

	/**
	 * Constructor.
	 */
	function ChapterEmbargo() {
		parent::SubmissionEmbargo();
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

}

?>
