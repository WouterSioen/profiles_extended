{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:addUserInfo}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfiles|ucfirst}: {$lblAddUserInfo}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtTitle} {$txtTitleError}
				</p>
				<p>
					<label for="title">{$lblType|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$ddmType} {$ddmTypeError}
				</p>
				<p class="hidden" id="showWhenEnum">
					<label for="title">{$lblValues|ucfirst}</label>
					{$txtValues} {$txtValuesError}
				</p>
			</fieldset>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddUserInfo|ucfirst}" />
		</div>
	</div>
{/form:addUserInfo}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
