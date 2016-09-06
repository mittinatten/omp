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
		{fbvFormSection id="allowRoleToSetEmbargo" label="manager.settings.embargoRoles" list="false"}
			{translate key="manager.settings.embargoRolesDescription"}
			{fbvElement type="checkbox" name="authorCanSetEmbargo" label="user.role.author" id="authorCanSetEmbargo" checked=$authorCanSetEmbargo}
			{fbvElement type="checkbox" name="editorCanSetEmbargo" label="user.role.editor" id="editorCanSetEmbargo" checked=$editorCanSetEmbargo}
	    {/fbvFormSection}
		{fbvFormSection id="selectEmbargoPeriods" label="manager.settings.embargoPeriods" list="false"}
			{translate key="manager.settings.embargoPeriodsDescription"}
			<div id="embargoPeriodsGridContainer">
				{url|assign:embargoPeriodsGridUrl router=$smarty.const.ROUTE_COMPONENT component="grid.settings.embargoPeriods.EmbargoPeriodsGridHandler" op="fetchGrid" escape=false}
				{load_url_in_div id="embargoPeriodsGridContainer" url=$embargoPeriodsGridUrl}
			</div>
			{fbvElement type="checkbox" name="allowPermanentEmbargo" label="manager.settings.allowPermanentEmbargo" id="allowPermanentEmbargo" checked=$allowPermanentEmbargo}
		{/fbvFormSection}
	{/fbvFormArea}
	
	{if !$wizardMode}
		{fbvFormButtons id="productionStageFormSubmit" submitText="common.save" hideCancel=true}
	{/if}
</form>