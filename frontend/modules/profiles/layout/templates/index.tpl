<section id="profile" class="mod">
	<div class="inner">
		<div class="bd">
			<p>
				<h3>{$settings.first_name} {$settings.last_name}</h3>
				{option:settings.avatar}
					<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$settings.avatar}" width="64" height="64" alt="" />
				{/option:settings.avatar}
				{option:!settings.avatar}
					<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$profile.displayName}" class="replaceWithFacebook" data-facebook-id="{option:settings.facebook_id}{$settings.facebook_id}{/option:settings.facebook_id}" />
				{/option:!settings.avatar}
				{option:age}{$age} {$lblYearsOld}{/option:age}{option:settings.city} - {$lblLivesIn} {$settings.city}{/option:settings.city}
			</p>
		</div>
	</div>
</section>