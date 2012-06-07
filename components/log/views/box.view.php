<table class="wrapper" width="100%">
<? for ($i=0; $i<$cols; $i++): ?><col width="<?=floor(100/$cols) ?>%" /><? endfor ?>
<tr>
<? $i = 0 ?>
<? foreach ($data as $key => $row): ?>
	<? if ($i && $i%$cols==0): ?></tr><td colspan="<?=$cols ?>"><hr></td><tr><? endif ?>
	<td>
	<div class="data_row">
		<?=$row->admin_buttons ?>
		<? $img = !empty($row->data->thumb) ? $row->data->thumb : (!empty($row->data->img_s) ? $row->data->img_s : (!empty($row->data->photo_s) ? $row->data->photo_s : '')) ?>
		
		<? if ($img): ?>
			<a class="avatar" href="<?=$row->data->full_link ?>"><img width="60" src="<?=$img ?>" alt="" /></a>
		<? endif ?>
		
		<a href="<?=$row->data->full_link ?>"><?=$row->data->title ?></a><br />
		<?=$row->model_name ?>
		<div class="small_info">
		<?=hlp::date($row->postdate) ?>
		</div>
		<div class="clr"></div>
	</div>
	</td>
<? $i++ ?>
<? endforeach ?>
</tr>
</table>