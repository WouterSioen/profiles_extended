<section id="profile" class="mod">
	<div class="inner">
		<div class="bd">
			<p class="profilePreview">
				{option:settings.avatar}
					<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$settings.avatar}" class="avatar" width="64" height="64" alt="" />
				{/option:settings.avatar}
				{option:!settings.avatar}
					<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$profile.displayName}" class="avatar replaceWithFacebook" data-facebook-id="{option:settings.facebook_id}{$settings.facebook_id}{/option:settings.facebook_id}" />
				{/option:!settings.avatar}
				<h2>{$settings.first_name} {$settings.last_name}</h2>
				{option:age}{$age} {$lblYearsOld}{/option:age}{option:settings.city} - {$lblLivesIn} {$settings.city}{/option:settings.city}
			</p>
			{option:info}
				<h4>{$lblInfo}</h4>
				{iteration:info}
					{option:info.value}
						{$info.title}: {$info.value}<br/>
					{/option:info.value}
				{/iteration:info}
			{/option:info}
		</div>
	</div>
</section>