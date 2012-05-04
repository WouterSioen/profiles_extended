{* Success *}
{option:registerIsSuccess}
	<div class="message success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Success *}
{option:updateSettingsSuccess}
	<div class="message success"><p>{$msgUpdateSettingsIsSuccess}</p></div>
{/option:updateSettingsSuccess}

{* Error *}
{option:updateSettingsHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:updateSettingsHasFormError}

<section id="settingsForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:updateSettings}
				<fieldset>
					<legend>{$lblAvatar|ucfirst}</legend>
					<p>
						{option:avatar}
							<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$avatar}" width="64" height="64" alt="" />
						{/option:avatar}
						{option:!avatar}
							<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$lblDisplayName|ucfirst}" class="replaceWithFacebook" {option:facebook_id}data-facebook-id="{$facebook_id}"{/option:facebook_id} />
						{/option:!avatar}
						<label for="avatar">{$lblAvatar|ucfirst}</label>
						{$fileAvatar} {$fileAvatarError}
						<span class="helpTxt">{$msgHelpAvatar}</span>
					</p>
					<p{option:txtDisplayNameError} class="errorArea"{/option:txtDisplayNameError}>
						<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtDisplayName}{$txtDisplayNameError}
						<small class="helpTxt">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
					</p>
				</fieldset>
				<fieldset>
					<legend>{$lblYourData|ucfirst}</legend>

					<div class="alignBlocks">
						<p{option:txtFirstNameError} class="errorArea"{/option:txtFirstNameError}>
							<label for="firstName">{$lblFirstName|ucfirst}</label>
							{$txtFirstName}{$txtFirstNameError}
						</p>
						<p{option:txtLastNameError} class="errorArea"{/option:txtLastNameError}>
							<label for="lastName">{$lblLastName|ucfirst}</label>
							{$txtLastName}{$txtLastNameError}
						</p>
					</div>
					<p{option:ddmGenderError} class="errorArea"{/option:ddmGenderError}>
						<label for="gender">{$lblGender|ucfirst}</label>
						{$ddmGender} {$ddmGenderError}
					</p>
					<p{option:ddmYearError} class="errorArea"{/option:ddmYearError}>
						<label for="day">{$lblBirthDate|ucfirst}</label>
						{$ddmDay} {$ddmMonth} {$ddmYear} {$ddmYearError}
					</p>
				</fieldset>
				<fieldset>
					<legend>{$lblYourLocationData|ucfirst}</legend>

					<div class="alignBlocks">
						<p{option:txtStreetError} class="errorArea"{/option:txtStreetError}>
							<label for="street">{$lblStreet|ucfirst}</label>
							{$txtStreet}{$txtStreetError}
						</p>
						<p{option:txtNumberError} class="errorArea"{/option:txtNumberError}>
							<label for="number">{$lblNumber|ucfirst}</label>
							{$txtNumber}{$txtNumberError}
						</p>
					</div>
					<div class="alignBlocks">
						<p{option:txtPostalCodeError} class="errorArea"{/option:txtPostalCodeError}>
							<label for="city">{$lblPostalCode|ucfirst}</label>
							{$txtPostalCode}{$txtPostalCodeError}
						</p>
						<p{option:txtCityError} class="errorArea"{/option:txtCityError}>
							<label for="city">{$lblCity|ucfirst}</label>
							{$txtCity}{$txtCityError}
						</p>
					</div>
					<p{option:ddmCountryError} class="errorArea"{/option:ddmCountryError}>
						<label for="country">{$lblCountry|ucfirst}</label>
						{$ddmCountry} {$ddmCountryError}
					</p>
				</fieldset>
				{option:fields}
					<fieldset>
						<legend>{$lblExtraInfo|ucfirst}</legend>
						{iteration:fields}
							<p>
								<label for="{$fields.label}">{$fields.label}</label>{$fields.txt}
							</p>
						{/iteration:fields}
					</fieldset>
				{/option:fields}
				<p>
					<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
				</p>
			{/form:updateSettings}
		</div>
	</div>
</section>