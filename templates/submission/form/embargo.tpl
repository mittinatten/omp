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
	{if count($embargoPeriods) > 1} {* only display the embargo month-picker if there are options to choose from *}
		{fbvElement type="select" id="embargoMonths" from=$embargoPeriods selected=$embargoMonths disabled=$readOnly label="submission.embargoPeriod" size=$fbvStyles.size.SMALL value=$embargoMonths}
	{/if}
{/fbvFormSection}