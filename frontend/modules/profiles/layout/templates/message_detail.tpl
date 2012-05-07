{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}

<section id="messages" class="{$thread_id}">
	<div class="inner">
		<p>
			<a href="{$var|geturlforblock:'profiles':'messages'}">{$lblBackToOverview}</a>
		</p>
		{option:load_more}
			<p class="ajaxLink">
				<span class="4" id="loadMessages">{$lblLoadOlderMessages}</span>
			</p>
		{/option:load_more}
		<div class="bd messages">
			{option:messages}
				{iteration:messages}
					<div class="messageHolder clearfix">
						<div class="imageHolder">
							{option:messages.avatar}
								<img src="{$FRONTEND_FILES_URL}/profiles/avatars/64x64/{$messages.avatar}" width="64" height="64" alt="" />
							{/option:messages.avatar}
							{option:!messages.avatar}
								<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="64" height="64" alt="{$messages.display_name}" class="replaceWithFacebook" {option:messages.facebook_id}data-facebook-id="{$messages.facebook_id}"{/option:messages.facebook_id} />
							{/option:!messages.avatar}
						</div>
						<div class="messageContent">
							<header class="hd">
								<h4><a href="{$var|geturlforblock:'profiles'}/{$messages.url}">{$messages.display_name}</a></h4>
								<ul>
									<li>{$messages.created_on|date:{$dateFormatLong}:{$LANGUAGE}} {$messages.created_on|date:'H:i'}</li>
								</ul>
							</header>
							<p>
								{$messages.text}
							</p>
						</div>
					</div>
				{/iteration:messages}
			{/option:messages}
		</div>
	</div>
</section>
<section id="messageForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:message}
				<p{option:txtMessageError} class="errorArea"{/option:txtMessageError}>
					<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtMessage}{$txtMessageError}
				</p>
				<p>
					<input class="inputSubmit" type="submit" value="{$lblSend|ucfirst}" />
				</p>
			{/form:message}
			<p>
				<a href="{$var|geturlforblock:'profiles':'messages'}">{$lblBackToOverview}</a>
			</p>
		</div>
	</div>
</section>