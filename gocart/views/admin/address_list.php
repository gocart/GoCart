<div style="border-bottom:1px solid #000000;clear:both;"></div>
			<?php
			$c = 1;
			foreach($address_list as $a):?>
				<div class="gc_photo" id="address_<?php echo $a['id'];?>">
					<div class="list_buttons">
						<input type="button" class="delete_address" rel="<?php echo $a['id'];?>" value="Delete"/>
						<input type="button" class="edit_address" rel="<?php echo $a['id'];?>" value="Edit"/>
					</div>
					<?php
					$b	= $a['field_data'];
					echo nl2br(format_address($b));
					?>
					
				</div>
			<?php endforeach;?>