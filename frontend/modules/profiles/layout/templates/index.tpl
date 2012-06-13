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
				<section id="info" class="mod">
					<h3>{$lblInfo}</h3>
					{iteration:info}
						{option:info.value}
							{$info.title}: {$info.value}<br/>
						{/option:info.value}
					{/iteration:info}
				</section>
			{/option:info}
			{option:activities}
				<section id="activities" class="mod">
					<h3>{$lblActivities}</h3>
					{iteration:activities}
						<div class="activity" id="{$activities.id}">
							{option:deletable}<span class="deleteButton"></span>{/option:deletable}
							{$settings.first_name} {$settings.last_name} {$activities.action} <a href="{$activities.url}">{$activities.title}</a>.
							<p class="date">{$activities.created_on|timeago}</p>
							<div class="comments">
								{option:activities.comments}
									{iteration:activities.comments}
										<div class="messageHolder clearfix" id="comment-{$activities.comments.id}">
											{option:deletable}<span class="deleteCommentButton"></span>{/option:deletable}
											{option:!deletable}
												{option:activities.comments.deletable}
													<span class="deleteCommentButton"></span>
												{/option:activities.comments.deletable}
												{option:!activities.comments.deletable}
													<span class="report"><a href="#report">{$lblReport}</a></span>
												{/option:!activities.comments.deletable}
											{/option:!deletable}
											<div class="imageHolder">
												{option:activities.comments.avatar}
													<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$activities.comments.avatar}" class="avatar" width="48" height="48" alt="" />
												{/option:activities.comments.avatar}
												{option:!activities.comments.avatar}
													<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$activities.comments.displayName}" class="avatar replaceWithFacebook" data-facebook-id="{option:activities.comments.facebook_id}{$activities.comments.facebook_id}{/option:activities.comments.facebook_id}" />
												{/option:!activities.comments.avatar}
											</div>
											<div class="messageContent">
												<p><a href="{$var|geturlforblock:'profiles'}/{$activities.comments.url}">{$activities.comments.username}</a>
												{$lblWrote} {$activities.comments.created_on|timeago}</p>
												<p>{$activities.comments.text}</p>
											</div>
										</div>
									{/iteration:activities.comments}
								{/option:activities.comments}
								<p class="bigInput">
									<input type="text" class="inputText">
									<input class="inputSubmit addComment" type="submit" value="{$lblComment|ucfirst}">
								</p>
							</div>
						</div>
					{/iteration:activities}
				</section>
			{/option:activities}
		</div>
		<span id="profileId" class="hidden">{$loggedInProfileId}</span>
	</div>
</section>