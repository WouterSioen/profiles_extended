{* Success *}
{option:registerIsSuccess}
	<div class="message success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Error *}
{option:registerHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm" class="mod">
			<div class="inner">
				<div class="bd">
					<fieldset>
						<p{option:txtFirstNameError} class="errorArea"{/option:txtFirstNameError}>
							<label for="first_name">{$lblFirstName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtFirstName}{$txtFirstNameError}
						</p>
						<p{option:txtLastNameError} class="errorArea"{/option:txtLastNameError}>
							<label for="last_name">{$lblLastName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtLastName}{$txtLastNameError}
						</p>
						<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}{$txtPasswordError}
						</p>
						<p>
							<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst} </label>
						</p>
						<p{option:chkAcceptTermsError} class="errorArea"{/option:chkAcceptTermsError}>
							<label for="acceptTerms">{$chkAcceptTerms} {$lblAcceptTerms|ucfirst} </label>
						</p>
						<p>
							<input class="inputSubmit" type="submit" value="{$lblRegister|ucfirst}" />
						</p>
					</fieldset>
				</div>
			</div>
		</section>
	{/form:register}
{/option:!registerHideForm}

{option:!registerPartTwoHideForm}
	{form:registerPartTwo}
		<section id="registerForm" class="mod">
			<div class="inner">
				<div class="bd">
					<fieldset>
						<p{option:ddmGenderError} class="errorArea"{/option:ddmGenderError}>
							<label for="gender">{$lblGender|ucfirst}</label>
							{$ddmGender} {$ddmGenderError}
						</p>
						<p{option:ddmYearError} class="errorArea"{/option:ddmYearError}>
							<label for="day">{$lblBirthDate|ucfirst}</label>
							{$ddmDay} {$ddmMonth} {$ddmYear} {$ddmYearError}
						</p>
						<p>
							<label for="newslettre">{$chkNewslettre} {$lblNewslettre|ucfirst} </label>
						</p>
						<p>
							<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
						</p>
					</fieldset>
				</div>
			</div>
		</section>
	{/form:registerPartTwo}
{/option:!registerPartTwoHideForm}