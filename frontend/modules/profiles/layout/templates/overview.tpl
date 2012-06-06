<section id="overview" class="mod">
	<div class="inner">
		<div class="bd">
			{$lblAlphabeticalList}
			<ul class="alphabet">
				{iteration:alphabet}
					<li><a href="{$var|geturlforblock:'profiles':'overview'}?lettre={$alphabet.lettre}">{$alphabet.lettre}</a></li>
				{/iteration:alphabet}
			</ul>
			{form:search}
				<p{option:txtToError} class="oneLiner errorArea"{/option:txtToError}>
					<label for="profile">{$lblProfile|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtProfile}{$txtProfileError}
					<input class="inputSubmit" type="submit" value="{$lblSearch|ucfirst}" />
				</p>
			{/form:search}
			{option:profiles}
				<div class="profiles">
					{iteration:profiles}
						<div class="profilePreview">
							{option:profiles.avatar}
								<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$profiles.avatar}" class="avatar" width="64" height="64" alt="" />
							{/option:profiles.avatar}
							{option:!profiles.avatar}
								<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$profiles.display_name}" class="avatar replaceWithFacebook" data-facebook-id="{option:profiles.facebook_id}{$profiles.facebook_id}{/option:profiles.facebook_id}" />
							{/option:!profiles.avatar}
							<a href="{$var|geturlforblock:'profiles'}/{$profiles.url}">{$profiles.first_name} {$profiles.last_name}</a>
						</div>
					{/iteration:profiles}
				</div>
				{option:loadMore}
					<a href="#loadMore" id="loadProfiles" class="button">Load More</a>
				{/option:loadMore}
			{/option:profiles}
			{option:!profiles}
				{$lblNoProfilesFound}
			{/option:!profiles}
		</div>
	</div>
</section>