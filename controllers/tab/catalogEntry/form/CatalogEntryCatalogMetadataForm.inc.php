<?php

/**
 * @file controllers/tab/catalogEntry/form/CatalogEntryCatalogMetadataForm.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CatalogEntryCatalogMetadataForm
 * @ingroup controllers_tab_catalogEntry_form_CatalogEntryCatalogMetadataForm
 *
 * @brief Displays a submission's catalog metadata entry form.
 */

// Image tpye.
define('SUBMISSION_IMAGE_TYPE_THUMBNAIL', 1);

import('lib.pkp.classes.form.Form');

class CatalogEntryCatalogMetadataForm extends Form {

	/** @var $_monograph Monograph The monograph used to show metadata information */
	var $_monograph;

	/** @var $_publishedMonograph PublishedMonograph The published monograph associated with this monograph */
	var $_publishedMonograph;

	/** @var $_stageId int The current stage id */
	var $_stageId;

	/** @var $_userId int The current user ID */
	var $_userId;

	/** @var $_imageExtension string Cover image extension */
	var $_imageExtension;

	/** @var $_sizeArray array Cover image information from getimagesize */
	var $_sizeArray;

	/** @var $_enableMonographEmbargo bool Does the press allow monograph embargoes*/
	var $_enableMonographEmbargo;

	/** @var $_enableChapterEmbargo bool Does the press allow chapter embargoes*/
	var $_enableChapterEmbargo;

	/**
	 * Parameters to configure the form template.
	 */
	var $_formParams;

	/**
	 * Constructor.
	 * @param $monographId integer
	 * @param $userId integer
	 * @param $stageId integer
	 * @param $formParams array
	 */
	function CatalogEntryCatalogMetadataForm($monographId, $userId, $stageId = null, $formParams = null) {
		parent::Form('controllers/tab/catalogEntry/form/catalogMetadataFormFields.tpl');
		$monographDao = DAORegistry::getDAO('MonographDAO');
		$this->_monograph = $monographDao->getById($monographId);

		$pressSettingsDao = DAORegistry::getDAO('PressSettingsDAO');
		$this->_enableMonographEmbargo = $pressSettingsDao->getSetting($this->_monograph->getPressId(), 'enableMonographEmbargo');
		$this->_enableChapterEmbargo = $pressSettingsDao->getSetting($this->_monograph->getPressId(), 'enableChapterEmbargo');

		$this->_stageId = $stageId;
		$this->_formParams = $formParams;
		$this->_userId = $userId;

		$this->addCheck(new FormValidatorURL($this, 'licenseURL', 'optional', 'form.url.invalid'));
	}

	/**
	 * Fetch the HTML contents of the form.
	 * @param $request PKPRequest
	 * return string
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('submissionId', $this->getMonograph()->getId());
		$templateMgr->assign('stageId', $this->getStageId());
		$templateMgr->assign('formParams', $this->getFormParams());

		$onixCodelistItemDao = DAORegistry::getDAO('ONIXCodelistItemDAO');

		// get the lists associated with the select elements on this form
		$audienceCodes = $onixCodelistItemDao->getCodes('List28');
		$audienceRangeQualifiers = $onixCodelistItemDao->getCodes('List30');
		$audienceRanges = $onixCodelistItemDao->getCodes('List77');

		// assign these lists to the form for select options
		$templateMgr->assign('audienceCodes', $audienceCodes);
		$templateMgr->assign('audienceRangeQualifiers', $audienceRangeQualifiers);
		$templateMgr->assign('audienceRanges', $audienceRanges);

		$monograph = $this->getMonograph();
		$publishedMonograph = $this->getPublishedMonograph();

		if ($monograph) {
			$submissionEmbargoDao = DAORegistry::getDAO('SubmissionEmbargoDAO');
			$templateMgr->assign('enableMonographEmbargo', $this->getEnableMonographEmbargo());
			if ($this->getEnableMonographEmbargo()) {
				$embargo = $submissionEmbargoDao->getObject($monograph->getId());
				if ($embargo) {
					$templateMgr->assign('embargoDate', $embargo->getEmbargoDate());
				}
			}

			$templateMgr->assign('enableChapterEmbargo', $this->getEnableChapterEmbargo());
			if ($this->getEnableChapterEmbargo()) {
				$chapterDao = DAORegistry::getDAO('ChapterDAO');
				$chapterEmbargoDao = DAORegistry::getDAO('ChapterEmbargoDAO');
				$chapters = $chapterDao->getChapters($monograph->getId());
				$chapter = $chapters->next();
				$chapterEmbargoes = array();
				while ($chapter) {
					$tmpChapter = array('title' => $chapter->getLocalizedFullTitle(), 'embargoDate' => null);
					$chapterEmbargo = $chapterEmbargoDao->getObject($chapter->getID());
					if ($chapterEmbargo) {
						$tmpChapter['embargoDate'] = $chapterEmbargo->getEmbargoDate();
					}
					$chapterEmbargoes[$this->getChapterEmbargoFormId($chapter)] = $tmpChapter;
					$chapter = $chapters->next();
				}
				$templateMgr->assign('chapterEmbargoes', $chapterEmbargoes);
			}
		}

		if ($publishedMonograph) {

			// pre-select the existing values on the form.
			$templateMgr->assign('audience', $publishedMonograph->getAudience());
			$templateMgr->assign('audienceRangeQualifier', $publishedMonograph->getAudienceRangeQualifier());
			$templateMgr->assign('audienceRangeFrom', $publishedMonograph->getAudienceRangeFrom());
			$templateMgr->assign('audienceRangeTo', $publishedMonograph->getAudienceRangeTo());
			$templateMgr->assign('audienceRangeExact', $publishedMonograph->getAudienceRangeExact());
			$templateMgr->assign('coverImage', $publishedMonograph->getCoverImage());
		}

		return parent::fetch($request);
	}

	function initData() {
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_COMMON,
			LOCALE_COMPONENT_PKP_SUBMISSION,
			LOCALE_COMPONENT_APP_SUBMISSION
		);

		$submission = $this->getMonograph();
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
		$this->_publishedMonograph = $publishedMonographDao->getById($submission->getId(), null, false);

		$copyrightHolder = $submission->getCopyrightHolder(null);
		$copyrightYear = $submission->getCopyrightYear();
		$licenseURL = $submission->getLicenseURL();

		$pressSettingsDao = DAORegistry::getDAO('PressSettingsDAO');

		$this->_data = array(
			'copyrightHolder' => $submission->getDefaultCopyrightHolder(null), // Localized
			'copyrightYear' => $submission->getDefaultCopyrightYear(),
			'licenseURL' => $submission->getDefaultLicenseURL(),
			'arePermissionsAttached' => !empty($copyrightHolder) || !empty($copyrightYear) || !empty($licenseURL),
			'confirm' => ($this->_publishedMonograph && $this->_publishedMonograph->getDatePublished())?true:false,
		);
	}


	//
	// Getters and Setters
	//
	/**
	 * Get the Monograph
	 * @return Monograph
	 */
	function getMonograph() {
		return $this->_monograph;
	}

	/**
	 * Get the PublishedMonograph
	 * @return PublishedMonograph
	 */
	function getPublishedMonograph() {
		return $this->_publishedMonograph;
	}

	/**
	 * Get the stage id
	 * @return int
	 */
	function getStageId() {
		return $this->_stageId;
	}

	/**
	 * Are monograph embargoes enabled
	 * @return bool
	 */
	function getEnableMonographEmbargo() {
		return $this->_enableMonographEmbargo;
	}

	/**
	 * Are chapter embargoes enabled
	 * @return bool
	 */
	function getEnableChapterEmbargo() {
		return $this->_enableChapterEmbargo;
	}

	/**
	 * Get ID for chapter embargo date form element
	 * @param chapter Chapter
	 * @return string Of the form 'chapterEmbargoDate_XX' where XX is the chapter ID.
	 */
	function getChapterEmbargoFormId($chapter) {
		return 'chapterEmbargoDate_' . $chapter->getId();
	}

	/**
	 * Get the extra form parameters.
	 */
	function getFormParams() {
		return $this->_formParams;
	}


	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$vars = array(
			'audience', 'audienceRangeQualifier', 'audienceRangeFrom', 'audienceRangeTo', 'audienceRangeExact',
			'copyrightYear', 'copyrightHolder', 'licenseURL', 'attachPermissions',
			'temporaryFileId', // Cover image
			'confirm',
		);

		if ($this->getEnableMonographEmbargo()) {
			$vars[] = 'embargoDate';
		}

		if ($this->getEnableChapterEmbargo()) {
			$chapterDao = DAORegistry::getDAO('ChapterDAO');
			$chapters = $chapterDao->getChapters($this->getMonograph()->getId());
			$chapter = $chapters->next();
			while ($chapter) {
				$vars[] = $this->getChapterEmbargoFormId($chapter);
				$chapter = $chapters->next();
			}
		}

		$this->readUserVars($vars);
	}

	/**
	 * Validate the form.
	 * @return boolean
	 */
	function validate() {
		// If a cover image was uploaded, make sure it's valid
		if ($temporaryFileId = $this->getData('temporaryFileId')) {
			import('lib.pkp.classes.file.TemporaryFileManager');
			$temporaryFileManager = new TemporaryFileManager();
			$temporaryFileDao = DAORegistry::getDAO('TemporaryFileDAO');
			$temporaryFile = $temporaryFileDao->getTemporaryFile($temporaryFileId, $this->_userId);
			if (	!$temporaryFile ||
				!($this->_imageExtension = $temporaryFileManager->getImageExtension($temporaryFile->getFileType())) ||
				!($this->_sizeArray = getimagesize($temporaryFile->getFilePath())) ||
				$this->_sizeArray[0] <= 0 || $this->_sizeArray[1] <= 0
			) {
				$this->addError('temporaryFileId', __('form.invalidImage'));
				return false;
			}
		}
		if ($this->getEnableMonographEmbargo() || $this->getEnableChapterEmbargo()) {
			$validator = new ValidatorDate();
			$hasMonographEmbargo = false;
			$hasChapterEmbargo = false;
			if ($this->getEnableMonographEmbargo()) {
				$embargoDate = $this->getData('embargoDate');
				if ($embargoDate && !($validator->isValid($embargoDate))) {
					$this->addError('embargoDate', __('submission.catalogEntry.monographEmbargoDateInvalid') . ' : \'' . $embargoDate . '\'');
				}
				if ($embargoDate) {
					$hasMonographEmbargo = true;
				}
			}

			if ($this->getEnableChapterEmbargo()) {
				$chapterDao = DAORegistry::getDAO('ChapterDAO');
				$chapters = $chapterDao->getChapters($this->getMonograph()->getId());
				$chapter = $chapters->next();
				while ($chapter) {
					$chapterEmbargoDate = $this->getData($this->getChapterEmbargoFormId($chapter));
					if ($chapterEmbargoDate && !($validator->isValid($chapterEmbargoDate))) {
						$this->addError($this->getChapterEmbargoFormId($chapter),  __('submission.catalogEntry.chapterEmbargoDateInvalid') . ' : \'' . $chapterEmbargoDate . '\'');
					}
					if ($chapterEmbargoDate) {
						$hasChapterEmbargo = true;
					}
					$chapter = $chapters->next();
				}
			}
			if ($hasMonographEmbargo && $hasChapterEmbargo) {
				$this->addError('embargoDate', __('submission.catalogEntry.doubleEmbargoError'));
			}
		}

		return parent::validate();
	}

	/**
	 * Save the metadata and store the catalog data for this published
	 * monograph.
	 */
	function execute($request) {
		parent::execute();

		$monograph = $this->getMonograph();
		$monographDao = DAORegistry::getDAO('MonographDAO');
		$publishedMonographDao = DAORegistry::getDAO('PublishedMonographDAO');
		$publishedMonograph = $publishedMonographDao->getById($monograph->getId(), null, false); /* @var $publishedMonograph PublishedMonograph */
		$isExistingEntry = $publishedMonograph?true:false;
		if (!$publishedMonograph) {
			$publishedMonograph = $publishedMonographDao->newDataObject();
			$publishedMonograph->setId($monograph->getId());
		}

		// Populate the published monograph with the cataloging metadata
		$publishedMonograph->setAudience($this->getData('audience'));
		$publishedMonograph->setAudienceRangeQualifier($this->getData('audienceRangeQualifier'));
		$publishedMonograph->setAudienceRangeFrom($this->getData('audienceRangeFrom'));
		$publishedMonograph->setAudienceRangeTo($this->getData('audienceRangeTo'));
		$publishedMonograph->setAudienceRangeExact($this->getData('audienceRangeExact'));

		// If a cover image was uploaded, deal with it.
		if ($temporaryFileId = $this->getData('temporaryFileId')) {
			// Fetch the temporary file storing the uploaded library file
			$temporaryFileDao = DAORegistry::getDAO('TemporaryFileDAO');
			$temporaryFile = $temporaryFileDao->getTemporaryFile($temporaryFileId, $this->_userId);
			$temporaryFilePath = $temporaryFile->getFilePath();
			import('classes.file.SimpleMonographFileManager');
			$simpleMonographFileManager = new SimpleMonographFileManager($monograph->getPressId(), $publishedMonograph->getId());
			$basePath = $simpleMonographFileManager->getBasePath();

			// Delete the old file if it exists
			$oldSetting = $publishedMonograph->getCoverImage();
			if ($oldSetting) {
				$simpleMonographFileManager->deleteFile($basePath . $oldSetting['thumbnailName']);
				$simpleMonographFileManager->deleteFile($basePath . $oldSetting['name']);
			}

			// The following variables were fetched in validation
			assert($this->_sizeArray && $this->_imageExtension);

			// Load the cover image for surrogate production
			$cover = null; // Scrutinizer
			switch ($this->_imageExtension) {
				case '.jpg': $cover = imagecreatefromjpeg($temporaryFilePath); break;
				case '.png': $cover = imagecreatefrompng($temporaryFilePath); break;
				case '.gif': $cover = imagecreatefromgif($temporaryFilePath); break;
			}
			assert(isset($cover));

			// Copy the new file over (involves creating the appropriate subdirectory too)
			$filename = 'cover' . $this->_imageExtension;
			$simpleMonographFileManager->copyFile($temporaryFile->getFilePath(), $basePath . $filename);

			// Generate thumbnail image
			$press = $request->getPress();
			$coverThumbnailsMaxWidth = $press->getSetting('coverThumbnailsMaxWidth');
			$coverThumbnailsMaxHeight = $press->getSetting('coverThumbnailsMaxHeight');

			$thumbnailImageInfo = $this->_buildSurrogateImage($cover, $basePath, SUBMISSION_IMAGE_TYPE_THUMBNAIL, $coverThumbnailsMaxWidth, $coverThumbnailsMaxHeight);

			// Clean up
			imagedestroy($cover);

			$publishedMonograph->setCoverImage(array(
				'name' => $filename,
				'width' => $this->_sizeArray[0],
				'height' => $this->_sizeArray[1],
				'thumbnailName' => $thumbnailImageInfo['filename'],
				'thumbnailWidth' => $thumbnailImageInfo['width'],
				'thumbnailHeight' => $thumbnailImageInfo['height'],
				'uploadName' => $temporaryFile->getOriginalFileName(),
				'dateUploaded' => Core::getCurrentDate(),
			));

			// Clean up the temporary file
			import('lib.pkp.classes.file.TemporaryFileManager');
			$temporaryFileManager = new TemporaryFileManager();
			$temporaryFileManager->deleteFile($temporaryFileId, $this->_userId);
		}

		if ($this->getData('attachPermissions')) {
			$monograph->setCopyrightYear($this->getData('copyrightYear'));
			$monograph->setCopyrightHolder($this->getData('copyrightHolder'), null); // Localized
			$monograph->setLicenseURL($this->getData('licenseURL'));
		} else {
			$monograph->setCopyrightYear(null);
			$monograph->setCopyrightHolder(null, null);
			$monograph->setLicenseURL(null);
		}
		$monographDao->updateObject($monograph);

		// Update the modified fields or insert new.
		if ($isExistingEntry) {
			$publishedMonographDao->updateObject($publishedMonograph);
		} else {
			$publishedMonographDao->insertObject($publishedMonograph);
		}

		import('classes.publicationFormat.PublicationFormatTombstoneManager');
		$publicationFormatTombstoneMgr = new PublicationFormatTombstoneManager();
		$publicationFormatDao = DAORegistry::getDAO('PublicationFormatDAO');
		$publicationFormatFactory = $publicationFormatDao->getBySubmissionId($monograph->getId());
		$publicationFormats = $publicationFormatFactory->toAssociativeArray();
		$notificationMgr = new NotificationManager();

		// set embargo
		if ($this->getEnableMonographEmbargo()) {
			$embargoDao = DAORegistry::getDAO('SubmissionEmbargoDAO');
			$embargo = $embargoDao->getObject($monograph->getId());
			$isExistingEmbargo = !is_null($embargo);
			if (!$isExistingEmbargo) {
				$embargo = new Embargo();
				$embargo->setAssociatedId($monograph->getId());
			}
			$embargoDate = $this->getData('embargoDate');

			$embargo->setEmbargoDate($embargoDate);
			if ($isExistingEmbargo) {
				$embargoDao->updateObject($embargo);
			} else {
				$embargoDao->insertObject($embargo);
			}
		}
		if ($this->getEnableChapterEmbargo()) {
			$chapterDao = DAORegistry::getDAO('ChapterDAO');
			$chapterEmbargoDao = DAORegistry::getDAO('ChapterEmbargoDAO');
			$chapters = $chapterDao->getChapters($monograph->getId());
			$chapter = $chapters->next();
			$chapterEmbargoes = array();
			while ($chapter) {
				$chapterEmbargo = $chapterEmbargoDao->getObject($chapter->getID());
				$isExistingEmbargo = !is_null($embargo);
				if (!$isExistingEmbargo) {
					$chapterEmbargo = new Embargo();
					$chapterEmbargo->setAssociatedId($monograph->getId());
				}
				$chapterEmbargoDate = $this->getData($this->getChapterEmbargoFormId($chapter));
				$chapterEmbargo->setEmbargoDate($chapterEmbargoDate);
				if ($isExistingEmbargo) {
					$chapterEmbargoDao->updateObject($chapterEmbargo);
				} else {
					$chapterEmbargoDao->insertObject($chapterEmbargo);
				}
				$chapter = $chapters->next();
			}
		}

		if ($this->getData('confirm')) {
			// Update the monograph status.
			$monograph->setStatus(STATUS_PUBLISHED);
			$monographDao->updateObject($monograph);

			$publishedMonograph->setDatePublished(Core::getCurrentDate());
			$publishedMonographDao->updateObject($publishedMonograph);

			$notificationMgr->updateNotification(
				$request,
				array(NOTIFICATION_TYPE_APPROVE_SUBMISSION),
				null,
				ASSOC_TYPE_MONOGRAPH,
				$publishedMonograph->getId()
			);

			// Remove publication format tombstones.
			$publicationFormatTombstoneMgr->deleteTombstonesByPublicationFormats($publicationFormats);

			// Update the search index for this published monograph.
			import('classes.search.MonographSearchIndex');
			MonographSearchIndex::indexMonographMetadata($monograph);

			// Log the publication event.
			import('lib.pkp.classes.log.SubmissionLog');
			SubmissionLog::logEvent($request, $monograph, SUBMISSION_LOG_METADATA_PUBLISH, 'submission.event.metadataPublished');
		} else {
			if ($isExistingEntry) {
				// Update the monograph status.
				$monograph->setStatus(STATUS_QUEUED);
				$monographDao->updateObject($monograph);

				// Unpublish monograph.
				$publishedMonograph->setDatePublished(null);
				$publishedMonographDao->updateObject($publishedMonograph);

				$notificationMgr->updateNotification(
					$request,
					array(NOTIFICATION_TYPE_APPROVE_SUBMISSION),
					null,
					ASSOC_TYPE_MONOGRAPH,
					$publishedMonograph->getId()
				);

				// Create tombstones for each publication format.
				$publicationFormatTombstoneMgr->insertTombstonesByPublicationFormats($publicationFormats, $request->getContext());

				// Log the unpublication event.
				import('lib.pkp.classes.log.SubmissionLog');
				SubmissionLog::logEvent($request, $monograph, SUBMISSION_LOG_METADATA_UNPUBLISH, 'submission.event.metadataUnpublished');
			}
		}
	}

	/**
	 * Generates a surrogate image used as a thumbnail.
	 * @param resource $cover the cover image uploaded.
	 * @param string $basePath base file path.
	 * @param int $type the type of image to create.
	 * @return array the details for the image (dimensions, file name, etc).
	 */
	function _buildSurrogateImage($cover, $basePath, $type, $coverThumbnailsMaxWidth, $coverThumbnailsMaxHeight) {
		// Calculate the scaling ratio for each dimension.
		$maxWidth = 0;
		$maxHeight = 0;
		$surrogateFilename = null;

		switch ($type) {
			case SUBMISSION_IMAGE_TYPE_THUMBNAIL:
				$maxWidth = $coverThumbnailsMaxWidth;
				$maxHeight = $coverThumbnailsMaxHeight;
				$surrogateFilename = 'thumbnail' . $this->_imageExtension;
				break;
		}

		$xRatio = min(1, $maxWidth / $this->_sizeArray[0]);
		$yRatio = min(1, $maxHeight / $this->_sizeArray[1]);

		// Choose the smallest ratio and create the target.
		$ratio = min($xRatio, $yRatio);

		$surrogateWidth = round($ratio * $this->_sizeArray[0]);
		$surrogateHeight = round($ratio * $this->_sizeArray[1]);
		$surrogate = imagecreatetruecolor($surrogateWidth, $surrogateHeight);
		imagecopyresampled($surrogate, $cover, 0, 0, 0, 0, $surrogateWidth, $surrogateHeight, $this->_sizeArray[0], $this->_sizeArray[1]);

		switch ($this->_imageExtension) {
			case '.jpg': imagejpeg($surrogate, $basePath . $surrogateFilename); break;
			case '.png': imagepng($surrogate, $basePath . $surrogateFilename); break;
			case '.gif': imagegif($surrogate, $basePath . $surrogateFilename); break;
		}
		imagedestroy($surrogate);
		return array('filename' => $surrogateFilename, 'width' => $surrogateWidth, 'height' => $surrogateHeight);
	}
}

?>
