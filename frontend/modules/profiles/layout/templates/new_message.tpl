{* Error *}
{option:sendError}
	<div class="message error">
		<p>{$errFormError}</p>
	</div>
{/option:sendError}

<section id="messageForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:message}
				<p{option:txtToError} class="errorArea"{/option:txtToError}>
					<label for="to">{$lblTo|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtTo}{$txtToError}
				</p>
				<p{option:txtMessageError} class="errorArea"{/option:txtMessageError}>
					<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtMessage}{$txtMessageError}
				</p>
				<p>
					<input class="inputSubmit" type="submit" value="{$lblSend|ucfirst}" />
				</p>
			{/form:message}
		</div>
	</div>
</section>