{* Success *}
{option:!updatePasswordHasFormError}
	{option:updateSuccess}
		<div class="message success"><p>{$msgUpdateSuccess}</p></div>
	{/option:updateSuccess}
{/option:!updatePasswordHasFormError}

{* Error *}
{option:updateHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:updateHasFormError}

{option:nonactive}
	<div class="message error"><p>{$errNonActiveError}</p></div>
{/option:nonactive}

{option:facebook_registered}
	<div class="message"><p>{msgFacebookNoPasswordChange}</div>
{/option:facebook_registered}

{option:!facebook_registered}
<section id="updatePasswordForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:updatePassword}
				<fieldset>
					<legend>{$lblPassword|ucfirst}</legend>
					<p{option:txtNewPasswordError} class="errorArea"{/option:txtNewPasswordError}>
						<label for="newPassword">{$lblNewPassword|ucfirst}</label>
						{$txtNewPassword}{$txtNewPasswordError}
					</p>
					<p{option:txtRepeatNewPasswordError} class="errorArea"{/option:txtRepeatNewPasswordError}>
						<label for="repeatNewPassword">{$lblRepeatNewPassword|ucfirst}</label>
						{$txtRepeatNewPassword}{$txtRepeatNewPasswordError}
					</p>
				</fieldset>
				<fieldset>
					<legend>{$lblEmail|ucfirst}</legend>
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}</label>
						{$txtEmail}{$txtEmailError}
					</p>
				</fieldset>
				<fieldset>
					<p{option:txtCurrentPasswordError} class="errorArea"{/option:txtCurrentPasswordError}>
						<label for="currentPassword">{$lblCurrentPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtCurrentPassword}{$txtCurrentPasswordError}
					</p>
					<p>
						<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
					</p>
				</fieldset>
			{/form:updatePassword}
		</div>
	</div>
</section>
{/option:!facebook_registered}