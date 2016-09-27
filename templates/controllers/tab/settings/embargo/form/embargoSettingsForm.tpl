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
			{fbvElement type="checkbox" name="enableMonographEmbargo" label="manager.settings.embargoMonographEnable" id="enableMonographEmbargo" checked=$enableMonographEmbargo}
			{fbvElement type="checkbox" name="enableChapterEmbargo" label="manager.settings.embargoChapterEnable" id="enableChapterEmbargo" checked=$enableChapterEmbargo}
			{fbvElement type="checkbox" name="authorCanSetEmbargo" label="manager.settings.authorCanSetEmbargo" id="authorCanSetEmbargo" checked=$authorCanSetEmbargo}
	    {/fbvFormSection}
	{/fbvFormArea}
	
	<div class="separator"></div>

	{if !$wizardMode}
		{fbvFormButtons id="embargoSettingsFormSubmit" submitText="common.save" hideCancel=true}
	{/if}
</form>