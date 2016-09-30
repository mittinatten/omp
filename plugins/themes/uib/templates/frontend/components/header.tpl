{**
 * plugins/themes/uib/templates/frontend/components/header.tpl
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief UiB frontend site header.
 *
 * @uses $isFullWidth bool Should this page be displayed without sidebars? This
 *       represents a page-level override, and doesn't indicate whether or not
 *       sidebars have been configured for thesite.
 *}

{* Determine whether a logo or title string is being displayed, logo is always on in UiB theme *}
{assign var="showingLogo" value=true}

<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{if !$pageTitleTranslated}{translate|assign:"pageTitleTranslated" key=$pageTitle}{/if}
{include file="core:frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape|default:"index"} pkp_op_{$requestedOp|escape|default:"index"}{if $showingLogo} has_site_logo{/if}">
	<script type="text/javascript">
		// Initialise JS handler.
		$(function() {ldelim}
			$('body').pkpHandler(
				'$.pkp.controllers.SiteHandler',
				{ldelim}
					{if $isUserLoggedIn}
						inlineHelpState: {$initialHelpState},
					{/if}
					toggleHelpUrl: {url|json_encode page="user" op="toggleHelp" escape=false},
					toggleHelpOnText: {$toggleHelpOnText|json_encode},
					toggleHelpOffText: {$toggleHelpOffText|json_encode},
					{include file="core:controllers/notification/notificationOptions.tpl"}
				{rdelim});
		{rdelim});
	</script>
	<div class="pkp_structure_page">
		{* Header *}
		<header class="pkp_structure_head" id="headerNavigationContainer" role="banner">
			<div class="pkp_head_wrapper">
				<img class="uib_logo" src="{$baseUrl}/plugins/themes/uib/UiB-logo-inverted.svg" />
				<div class="pkp_site_name_wrapper">
					<div class="pkp_site_name">
						{if $currentJournal && $multipleContexts}
							{url|assign:"homeUrl" journal="index" router=$smarty.const.ROUTE_PAGE}
						{else}
							{url|assign:"homeUrl" page="index" router=$smarty.const.ROUTE_PAGE}
						{/if}
						{if $requestedOp == 'index'}
							<h1>
						{else}
							<div>
						{/if}
								<a href="{$homeUrl}" class="is_text">{$displayPageHeaderTitle}</a>
						{if $requestedOp == 'index'}
							</h1>
						{else}
							</div>
						{/if}
					</div>
				</div>

				{* Primary site navigation *}
				<script type="text/javascript">
					// Attach the JS file tab handler.
					$(function() {ldelim}
						$('#navigationPrimary').pkpHandler(
							'$.pkp.controllers.MenuHandler');
					{rdelim});
				</script>
				<nav class="pkp_navigation_primary_row" aria-label="{translate|escape key="common.navigation.site"}">
					<div class="pkp_navigation_primary_wrapper">

						{* Primary navigation menu for current application *}
						{include file="frontend/components/primaryNavMenu.tpl"}

						{* Search form *}
						{if !$noContextsConfigured}
							{include file="frontend/components/searchForm_simple.tpl"}
						{/if}
					</div>
				</nav>

				{* User-specific login, settings and task management *}
				{url|assign:fetchHeaderUrl router=$smarty.const.ROUTE_COMPONENT component="page.PageHandler" op="userNav" escape=false}
				{load_url_in_div class="pkp_navigation_user_wrapper" id="navigationUserWrapper" url=$fetchHeaderUrl}

			</div><!-- .pkp_head_wrapper -->
		</header><!-- .pkp_structure_head -->

		{* Wrapper for page content and sidebars *}
		{if $isFullWidth}
			{assign var=hasLeftSidebar value=0}
		{/if}
		<div class="pkp_structure_content{if $hasLeftSidebar} has_left_sidebar{/if}">

			<script type="text/javascript">
				// Attach the JS page handler to the main content wrapper.
				$(function() {ldelim}
					$('div.pkp_structure_main').pkpHandler('$.pkp.controllers.PageHandler');
				{rdelim});
			</script>

			<div class="pkp_structure_main" role="main">
