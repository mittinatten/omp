{**
 * controllers/tab/settings/embargoSettings/form/embargoSettingsForm.tpl
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Embargo settings form.
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#embargoSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="embargoSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="tab.settings.DistributionSettingsTabHandler" op="saveFormData" tab="embargo"}">
	{csrf}
	{fbvFormArea id="embargoSettings"}
		{fbvFormSection id="enableEmbargoContainer" label="manager.settings.embargoEnable" list="false"}
			{fbvElement type="checkbox" name="enableEmbargo" label="common.enable" id="enableEmbargo" checked=$enableEmbargo}
			{fbvElement type="checkbox" name="authorCanSetEmbargo" label="manager.settings.authorCanSetEmbargo" id="authorCanSetEmbargo" checked=$authorCanSetEmbargo}
	    {/fbvFormSection}
		{fbvFormSection id="selectEmbargoPeriods" label="manager.settings.embargoPeriods" list="false"}
			{translate key="manager.settings.embargoPeriodsDescription"}
			<div id="embargoPeriodsGridContainer">
				{url|assign:embargoPeriodsGridUrl router=$smarty.const.ROUTE_COMPONENT component="grid.settings.embargoPeriods.EmbargoPeriodsGridHandler" op="fetchGrid" escape=false}
				{load_url_in_div id="embargoPeriodsGridContainer" url=$embargoPeriodsGridUrl}
			</div>
		{/fbvFormSection}
		{fbvFormSection id="permanentEmbargo" label="manager.settings.permanentEmbargo" list="false"}
			{fbvElement type="checkbox" name="allowPermanentEmbargo" label="manager.settings.allowPermanentEmbargo" id="allowPermanentEmbargo" checked=$allowPermanentEmbargo}
		{/fbvFormSection}
		{fbvFormSection id="permanentEmbargoLength" label="manager.settings.permanentEmbargoPeriod" list="false"}
			{fbvElement type="text" name="permanentEmbargoPeriod" id="permanentEmbargoPeriod" value=$permanentEmbargoPeriod label="manager.settings.permanentEmbargoPeriodDescription"}
		{/fbvFormSection}
	{/fbvFormArea}
	
	<div class="separator"></div>

	{if !$wizardMode}
		{fbvFormButtons id="embargoPeriodsFormSubmit" submitText="common.save" hideCancel=true}
	{/if}
</form>