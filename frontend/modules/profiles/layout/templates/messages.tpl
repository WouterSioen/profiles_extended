{option:sentMessage}
	<div class="message success"><p>{$msgMessageSent}</p></div>
{/option:sentMessage}

<section id="messages" class="mod">
	<div class="inner">
		<div class="bd">
			<h3>{$lblMyMessages}</h3>
			{option:threads}
				{iteration:threads}
					<header class="hd">
						<h4><a href="{$var|geturlforblock:'profiles'}/{$threads.latestMessage.display_name}">{$threads.latestMessage.first_name} {$threads.latestMessage.last_name}</a></h4>
						<ul>
							<li>{$threads.latestMessage.created_on}</li>
						</ul>
					</header>
					<p>
						{$threads.latestMessage.text|truncate:'250'}
					</p>
				{/iteration:threads}
			{/option:threads}
			{option:!threads}
			{/option:!threads}
		</div>
	</div>
</section>