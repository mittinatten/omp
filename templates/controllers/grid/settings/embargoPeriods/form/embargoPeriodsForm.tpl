{**
 * templates/controllers/grid/settings/embargoPeriods/form/embargoPeriodsForm.tpl
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * EmbargoPeriods form under press management.
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#embargoPeriodsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="embargoPeriodsForm" method="post" action={url router=$smarty.const.ROUTE_COMPONENT component="grid.settings.embargoPeriods.EmbargoPeriodsGridHandler" op="updateEmbargoPeriod" embargoPeriodId=$embargoPeriodId}">
	{csrf}
	<input type="hidden" name="embargoPeriodId" value="{$embargoPeriodId|escape}">
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="seriesFormNotification"}
	{fbvFormArea id="addEmbargoPeriod" title="manager.settings.embargoPeriod"}
		{fbvElement label="manager.settings.specifyEmbargoPeriod" type="text" name="embargoPeriod" id="embargoPeriod" value=$embargoPeriod size=$fbvStyles.size.MEDIUM}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.add"}
</form>