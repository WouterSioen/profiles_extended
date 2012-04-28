<div id="profileDropDown">
	<div class="inner">
		<div class="bd">
			{option:profile.display_name}
			<a href="#" id="openProfilesDropdown" class="fakeDropdown">
				<span class="avatar">
					{option:profile.avatar}
						<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$profile.avatar}" alt="" />
					{/option:profile.avatar}
					{option:!profile.avatar}
						<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" alt="{$profile.display_name}" {option:profile.facebook_id}class="replaceWithFacebook" data-facebook-id="{profile.facebook_id}"{/option:profile.facebook_id} />
					{/option:!profile.avatar}
				</span>
				<span class="nickname">{$profile.display_name}{option:profile.count} ({$profile.count}){/option:profile.count}</span>
				<span class="arrow">&#x25BC;</span>
			</a>
			<ul class="hidden" id="ddProfiles">
				<li><a href="{$profile_url}/{$profile.url}">{$lblMyProfile}</a></li>
				<li><a href="{$messages_url}">{$lblMyMessages}{option:profile.count} ({$profile.count}){/option:profile.count}</a></li>
				<li><a href="{$settings_url}">{$lblSettings}</a></li>
				<li class="lastChild"><a href="{$logout_url}">Log uit</a></li>
			</ul>
			{/option:profile.display_name}
			{option:!profile.display_name}
				<span class="login"><a href="{$register_url}">{$lblRegister}</a> - <a href="{$login_url}">{$lblLogin}</a></span>
			{/option:!profile.display_name}
		</div>
	</div>
</div>