<?php

/**
 * @file classes/submission/form/SubmissionSubmitStep3Form.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionSubmitStep3Form
 * @ingroup submission_form
 *
 * @brief Form for Step 3 of author submission.
 */

import('lib.pkp.classes.submission.form.PKPSubmissionSubmitStep3Form');
import('classes.submission.SubmissionMetadataFormImplementation');

class SubmissionSubmitStep3Form extends PKPSubmissionSubmitStep3Form {
	/**
	 * Constructor.
	 */
	function SubmissionSubmitStep3Form($context, $submission) {
		parent::PKPSubmissionSubmitStep3Form(
			$context,
			$submission,
			new SubmissionMetadataFormImplementation($this)
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		parent::readInputData();

		// Include embargo data
		if ($this->context->getSetting('authorCanSetEmbargo')) {
			$this->readUserVars(array('embargoMonths'));
		}

		// Include category information.
		$this->readUserVars(array('categories'));

		// Load the series. This is used in the step 3 form to
		// determine whether or not to display indexing options.
		$seriesDao = DAORegistry::getDAO('SeriesDAO');
		$this->_data['series'] = $seriesDao->getById($this->submission->getSeriesId(), $this->submission->getContextId());
	}

	/**
	 * Fetch the form
	 * @param $request
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);

		// If categories are configured, present the LB.
		$categoryDao = DAORegistry::getDAO('CategoryDAO');
		$templateMgr->assign('categoriesExist', $categoryDao->getCountByPressId($this->context->getId()) > 0);

		// Get Embargo options for this context
		$authorCanSetEmbargo = $this->context->getSetting('authorCanSetEmbargo');
		$enableMonographEmbargo = $this->context->getSetting('enableMonographEmbargo');
		$templateMgr->assign('enableEmbargo', $authorCanSetEmbargo && $enableMonographEmbargo);
		// FIXME: this code is present both here and in ChapterForm, where to put it?
		if ($authorCanSetEmbargo && $enableMonographEmbargo) {
			$embargoPeriods = $this->context->getSetting('embargoPeriods');
			sort($embargoPeriods);
			$periodsOptions = array(0 => __('submission.submit.selectEmbargo'));
			foreach ($embargoPeriods as $i => $t) {
				if ($t == 0) continue;
				$periodsOptions += array($t => __('submission.embargoMonths', array('months' => $t)));
			}
			if ($this->context->getSetting('allowPermanentEmbargo')) {
				$periodsOptions += array($this->context->getSetting('permanentEmbargoPeriod') => __('submission.permanentEmbargo'));
			}
			$templateMgr->assign('embargoPeriods',  $periodsOptions);
		}

		return parent::fetch($request);
	}

	/**
	 * Save changes to submission.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return int the submission ID
	 */
	function execute($args, $request) {
		if ($this->context->getSetting('authorCanSetEmbargo')) {
			$embargoDAO = DAORegistry::getDAO('SubmissionEmbargoDAO');
			$embargo = $embargoDAO->getObject($this->submission->getId());
			$existingEmbargo = !is_null($embargo);
			if (!$existingEmbargo) {
				$embargo = new SubmissionEmbargo();
				$embargo->setSubmissionId($this->submission->getId());
			}
			$embargo->setEmbargoMonths($this->getData('embargoMonths'));
			if ($existingEmbargo) {
				$embargoDAO->updateObject($embargo);
			} else {
				$embargoDAO->insertObject($embargo);
			}
		}

		parent::execute($args, $request);

		// handle category assignment.
		ListbuilderHandler::unpack($request, $this->getData('categories'), array($this, 'deleteEntry'), array($this, 'insertEntry'), array($this, 'updateEntry'));

		return $this->submissionId;
	}

	/**
	 * Associate a category with a submission.
	 * @copydoc ListbuilderHandler::insertEntry
	 */
	function insertEntry($request, $newRowId) {

		$application = PKPApplication::getApplication();
		$request = $application->getRequest();

		$categoryId = $newRowId['name'];
		$categoryDao = DAORegistry::getDAO('CategoryDAO');
		$submissionDao = DAORegistry::getDAO('MonographDAO');
		$context = $request->getContext();
		$submission = $this->submission;

		$category = $categoryDao->getById($categoryId, $context->getId());
		if (!$category) return true;

		// Associate the category with the submission
		$submissionDao->addCategory($submission->getId(), $categoryId);
		return true;
	}

	/**
	 * Delete a category association.
	 * @copydoc ListbuilderHandler::deleteEntry
	 */
	function deleteEntry($request, $rowId) {
		if ($rowId) {
			$categoryDao = DAORegistry::getDAO('CategoryDAO');
			$submissionDao = DAORegistry::getDAO('MonographDAO');
			$category = $categoryDao->getById($rowId);
			if (!is_a($category, 'Category')) {
				assert(false);
				return false;
			}
			$submission = $this->submission;
			$submissionDao->removeCategory($submission->getId(), $rowId);
		}

		return true;
	}

	/**
	 * Update a category association.
	 * @copydoc ListbuilderHandler::updateEntry
	 */
	function updateEntry($request, $rowId, $newRowId) {
		$this->deleteEntry($request, $rowId);
		$this->insertEntry($request, $newRowId);
		return true;
	}
}

?>
