{* Error *}
{option:registerHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:registerHasFormError}
{option:registerHasEmailExistsError}
	<div class="message error"><p>{$errEmailExists}</p></div>
{/option:registerHasEmailExistsError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm" class="mod">
			<div class="inner">
				<div class="bd">
					<fieldset>
						<div class="showOnFacebookLogout hideOnFacebookLogin" {option:facebookUserData} style="display: none;"{/option:facebookUserData}>
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
						</div>
						{option:FACEBOOK_HAS_APP}
							<div class="facebookLoginWrapper">
								<h3>{$lblRegisterWithFacebook}</h3>
								<div class="facebookLoggedInAs showOnFacebookLogin hideOnFacebookLogout"{option:!facebookUserData} style="display: none;"{/option:!facebookUserData}>
									<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{option:facebookUserData}{$facebookUserData.name}{/option:facebookUserData}" class="replaceWithFacebook" data-facebook-id="{option:facebookUserData}{$facebookUserData.id}{/option:facebookUserData}" />
									{option:facebookUserData}{$msgFacebookLoggedInAs|sprintf:{$facebookUserData.name}:{$facebookUserData.link}} - <a href="#" class="facebookLogout">{$lblLogout|ucfirst}</a>{/option:facebookUserData}
									{option:!facebookUserData}{$msgFacebookLoggedInAs|sprintf:'':''} <a href="#" class="facebookLogout">{$lblLogout|ucfirst}</a>{/option:!facebookUserData}
								</div>
								<div class="facebookLogin showOnFacebookLogout hideOnFacebookLogin"{option:facebookUserData} style="display: none;"{/option:facebookUserData}>
									<div class="fb-login-button" data-show-faces="false" data-width="200" data-scope="email,user_birthday,user_location"></div>
								</div>
							</div>
						{/option:FACEBOOK_HAS_APP}
						
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
						{option:allowNewslettre}
							<p>
								<label for="newslettre">{$chkNewslettre} {$lblNewslettre|ucfirst} </label>
							</p>
						{/option:allowNewslettre}
						<p>
							<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
						</p>
					</fieldset>
				</div>
			</div>
		</section>
	{/form:registerPartTwo}
{/option:!registerPartTwoHideForm}