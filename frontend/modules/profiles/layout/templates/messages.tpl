{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}
<p>
	<a href="{$var|geturlforblock:'profiles':'new_message'}" id="newMessageButton" class="button">{$lblNewMessage}</a>
</p>
<section id="messages" class="{$user_id}">
	<div class="inner">
		<div class="bd threads">
			{option:threads}
				{iteration:threads}
					<div class="thread {$threads.id}">
						<header class="hd">
							<h4{option:!threads.status} class="unread"{/option:!threads.status}>
								<a href="{$var|geturlforblock:'profiles':'message_detail'}/{$threads.id}">
									{iteration:threads.receivers}
										{$threads.receivers.display_name}{option:!threads.receivers.last}, {/option:!threads.receivers.last}
									{/iteration:threads.receivers}
								</a>
							</h4>
							<ul>
								<li>{$threads.created_on|date:{$dateFormatLong}:{$LANGUAGE}} {$threads.created_on|date:'H:i'}</li>
							</ul>
						</header>
						<p>
							{$threads.text|truncate:'250'}
						</p>
						{option:!threads.status}
							<p class="ajaxLink">
								<span class="markRead" id="{$threads.id}" >{$lblMarkAsRead}</span>
							</p>
						{/option:!threads.status}
					</div>
				{/iteration:threads}
			{/option:threads}
			{option:!threads}
				{$lblNoThreads}
			{/option:!threads}
		</div>
		{option:load_more}
			<p class="ajaxLink">
				<span class="4" id="loadThreads">{$lblLoadOlderThreads}</span>
			</p>
		{/option:load_more}
	</div>
</section>