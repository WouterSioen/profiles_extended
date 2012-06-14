{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblUserinfo|ucfirst}</h2>

	{option:showProfilesAddUserInfo}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_user_info'}" class="button icon iconAdd">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showProfilesAddUserInfo}
</div>

<div class="dataGridHolder">
	{option:dgUserInfo}
		{$dgUserInfo}
	{/option:dgUserInfo}

	{option:!dgUserInfo}
		<p>{$msgNoItems}</p>
	{/option:!dgUserInfo}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
