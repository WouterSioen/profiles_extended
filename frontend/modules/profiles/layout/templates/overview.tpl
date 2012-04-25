<section id="profile" class="mod">
	<div class="inner">
		<div class="bd">
			{$lblAlphabeticalList}
			<ul>
				{iteration:alphabet}
					<li><a href="{$var|geturlforblock:'profiles':'overview'}?lettre={$alphabet.lettre}">{$alphabet.lettre}</a></li>
				{/iteration:alphabet}
			</ul>
			<h3>{$lblProfiles|ucfirst}</h3>
			{option:profiles}
				{iteration:profiles}
					{option:profiles.settings.avatar}
						<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$profiles.settings.avatar}" width="64" height="64" alt="" />
					{/option:profiles.settings.avatar}
					{option:!profiles.settings.avatar}
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{option:facebookUserData}{$facebookUserData.name}{/option:facebookUserData}" class="replaceWithFacebook" data-facebook-id="{option:facebookUserData}{$facebookUserData.id}{/option:facebookUserData}" />
					{/option:!profiles.settings.avatar}
					<a href="{$var|geturlforblock:'profiles'}/{$profiles.display_name}">{$profiles.settings.first_name} {$profiles.settings.last_name}</a>
				{/iteration:profiles}
			{/option:profiles}
			{option:!profiles}
				{$lblNoProfiles}
			{/option:!profiles}
		</div>
	</div>
</section>
