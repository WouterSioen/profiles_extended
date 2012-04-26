<section id="facebookSettings" class="mod">
	<div class="inner">
		<div class="bd">
			{option:FACEBOOK_HAS_APP}
				<div class="facebookLoginWrapper">
					<h3>{$lblFacebookSettings}</h3>
					<div class="facebookLoggedInAs showOnFacebookLogin hideOnFacebookLogout"{option:!facebookUserData} style="display: none;"{/option:!facebookUserData}>
						<p>
							{$lblAlreadyConnected}
						</p>
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{option:facebookUserData}{$facebookUserData.name}{/option:facebookUserData}" class="replaceWithFacebook" data-facebook-id="{option:facebookUserData}{$facebookUserData.id}{/option:facebookUserData}" />
						{option:facebookUserData}{$msgFacebookLoggedInAs|sprintf:{$facebookUserData.name}:{$facebookUserData.link}}{/option:facebookUserData}
						{option:!facebookUserData}{$msgFacebookLoggedInAs|sprintf:'':''}{/option:!facebookUserData}
					</div>
					<div class="facebookLogin showOnFacebookLogout hideOnFacebookLogin"{option:facebookUserData} style="display: none;"{/option:facebookUserData}>
						{$msgFacebookConnectInfo}
						<div class="fb-login-button" data-show-faces="false" data-width="200" data-scope="email,user_birthday,user_location"></div>
					</div>
				</div>
			{/option:FACEBOOK_HAS_APP}
		</div>
	</div>
</section>