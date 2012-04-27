<section id="overview" class="mod">
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
					{option:profiles.avatar}
						<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$profiles.avatar}" width="64" height="64" alt="" />
					{/option:profiles.avatar}
					{option:!profiles.avatar}
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$profiles.display_name}" class="replaceWithFacebook" data-facebook-id="{option:profiles.facebook_id}{$profiles.facebook_id}{/option:profiles.facebook_id}" />
					{/option:!profiles.avatar}
					<a href="{$var|geturlforblock:'profiles'}/{$profiles.url}">{$profiles.first_name} {$profiles.last_name}</a>
				{/iteration:profiles}
			{/option:profiles}
			{option:!profiles}
				{$lblNoProfilesFound}
			{/option:!profiles}
		</div>
	</div>
</section>