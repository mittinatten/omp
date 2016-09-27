{**
 * templates/submission/form/embargo.tpl
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Embargo settings for submission
 *}
{fbvFormSection label="submission.embargo" list="false"}
	{fbvElement type="text" id="embargoDate" disabled=$readOnly label="submission.date" size=$fbvStyles.size.SMALL value=$embargoDate|date_format:"%Y-%m-%d"}
{/fbvFormSection}