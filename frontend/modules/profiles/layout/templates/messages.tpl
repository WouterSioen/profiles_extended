{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}

<section id="messages" class="mod">
	<div class="inner">
		<div class="bd">
			<h3>{$lblMyMessages}</h3>
			<p>
				<a href="{$var|geturlforblock:'profiles':'new_message'}" class="button">{$lblNewMessage}</a>
			</p>
			
			{option:threads}
				{iteration:threads}
					<header class="hd">
						<h4>
							<a href="{$var|geturlforblock:'profiles':'message_detail'}/{$threads.id}">
								{iteration:threads.receivers}
									{$threads.receivers.display_name}{option:!threads.receivers.last}, {/option:!threads.receivers.last}
								{/iteration:threads.receivers}
							</a>
						</h4>
						<ul>
							<li>{$threads.created_on}</li>
						</ul>
					</header>
					<p>
						{$threads.text|truncate:'250'}
					</p>
				{/iteration:threads}
			{/option:threads}
			{option:!threads}
			{/option:!threads}
		</div>
	</div>
</section>