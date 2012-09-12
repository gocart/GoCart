	<hr/>
	<footer>
		<p>&copy; Clear Sky Designs <?php echo date('Y');?> &bull; Driven by GoCart</p>
	</footer>
</div>

<?php
/*
This is the anchor form for the goedit editor.
One day we'll integrate this into the js somehow
*/
?>
<div class="dialogs" style="display:none">
	<div id="goedit-create-anchor">
		<div style="padding:15px;">
			<div class="row-fluid">
				<label><?php echo lang('goedit_url');?></label>
				<input type="text" class="span12" value="" id="goedit-form-anchor-url"/>
			
				<label><?php echo lang('goedit_target');?></label>
				<select class="span12" id="goedit-form-anchor-target">
					<option value="_self"><?php echo lang('goedit_self');?></option>
					<option value="_blank"><?php echo lang('goedit_new_window');?></option>
				</select>
			
				<label><?php echo lang('goedit_class');?></label>
				<input type="text" class="span12" value="" id="goedit-form-anchor-class"/>

				<button class="btn" type="button" onclick="goedit_insert_link();"><?php echo lang('goedit_insert_link');?></button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
