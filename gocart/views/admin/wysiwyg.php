<?php if(empty($wyisyg_loaded)): $wyisyg_loaded = true;?>
	<link href="/assets/css/wysiwyg.css" rel="stylesheet" />
<?php endif; ?>

<div class="btn-toolbar" style="margin:0px; margin-top:3px;">
	<div class="btn-group">
		<button type="button" class="command btn" alt="bold"><i class="sprite-text-bold"></i></button>
		<button type="button" class="command btn" alt="italic"><i class="sprite-text-italic"></i></button>
		<button type="button" class="command btn" alt="underline"><i class="sprite-text-underline"></i></button>
	</div>

	<div class="btn-group">
		<button type="button" class="command btn" alt="justifyleft"><i class="sprite-text-align-left"></i></button>
		<button type="button" class="command btn" alt="justifycenter"><i class="sprite-text-align-center"></i></button>
		<button type="button" class="command btn" alt="justifyright"><i class="sprite-text-align-right"></i></button>
		<button type="button" class="command btn" alt="justifyfull"><i class="sprite-text-align-justify"></i></button>
	</div>

	<div class="btn-group">
		<button type="button" class="command btn" alt="insertorderedlist"><i class="sprite-text-list-numbers"></i></button>
		<button type="button" class="command btn" alt="insertunorderedlist"><i class="sprite-text-list-bullets"></i></button>
	</div>
</div>

<div class="editable" contentEditable="true">
</div>

<script type="text/javascript">
//add click events
$('.command').click( function(){
	run_command($(this).attr('alt'), null, false);
});

function set_span(span)
{
	if(span != '')
	{
		currentElement.parent().attr('class', 'span'+span);
	}
	$('#columns-select').val('');
}

function run_command(cmd, intfc, val)
{
	if(!document.execCommand(cmd, intfc, val))
	{
		alert('error');
	}
}
</script>