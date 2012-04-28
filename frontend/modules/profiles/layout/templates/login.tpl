{* Error *}
{option:formError}
	<div class="message error">
		{option:loginError}
			<p>{$loginError}</p>
		{/option:loginError}

		{option:!loginError}
			<p>{$errFormError}</p>
		{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:login}
				<div class="showOnFacebookLogout hideOnFacebookLogin" {option:facebookUserData} style="display: none;"{/option:facebookUserData}>
					<fieldset>
						<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}{$txtPasswordError}
						</p>
						<p>
							<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
							{$chkRememberError}
						</p>
					</fieldset>
				</div>
				{option:FACEBOOK_HAS_APP}
					<div class="facebookLoginWrapper">
						<h3>{$lblLoginWithFacebook}</h3>
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
				<fieldset>
					<p>
						<input class="inputSubmit" type="submit" value="{$lblLogin|ucfirst}" />
					</p>
				</fieldset>
			{/form:login}
		</div>
		<footer class="ft">
			<p>
				<a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a>
			</p>
			<p>
				{$msgNoAccountYet} <a href="{$var|geturlforblock:'profiles':'register'}">{$msgRegisterNow}</a>
			</p>
		</footer>
	</div>
</section>