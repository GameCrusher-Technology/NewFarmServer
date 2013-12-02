<?php include renderTemplate('header'); ?>
<?php include renderTemplate('error_msg'); ?>
<form method="post" action="admincp.php?mod=user&act=databasemgr" id="form1" name="form1">
<table>
	<caption>
	<h1>用户数据管理</h1>
	</caption>
	<tr>
		<td width="120" align="center">用户uid:</td>
		<td>
			<input type="text" id="uid" name="uid" value="<?php echo $uid;?>"/>
		</td>
	</tr>
	<tr>
		<td width="120" align="center">gameuid:</td>
		<td>
			<input type="text" id="gameuid" name="gameuid" value="<?php echo $gameuid;?>"/>
		</td>
	</tr>
	<tr>
		<td align="center">数据库表:</td>
		<td>
			<select id="table_name" name="table_name"">
				<option value="">请选择数据库表</option>
				<?php foreach($table_names as $table_name_tmp) {?>
				<?php if($table_name_tmp == $table_name) { ?>
				<option value="<?php echo $table_name_tmp;?>" selected="selected"><?php echo $table_name_tmp;?></option>
				<?php } else { ?>
				<option value="<?php echo $table_name_tmp;?>"><?php echo $table_name_tmp;?></option>
				<?php } ?>
				<?php } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="120" align="center">系统留言[例如:<font color="red">管理员送给你了一颗番茄种子</font>]:</td>
		<td>
			<input type="text" name="event_msg" value="<?php echo $event_msg;?>" size="100" maxlength="255"/>
		</td>
	</tr>
	<?php foreach($table_names as $table_name_tmp) {?>
	<tr id="<?php echo $table_name_tmp;?>_insert_info" style="display:none" bgcolor="#FFE4B5">
		<td width="150" align="center" colspan="2">
			<table>
				<tr><td colspan="2">添加<?php echo $table_name_tmp;?>数据</td></tr>
				<?php $field_names = array_keys($fields_defs[$table_name_tmp]);?>
				<?php foreach($field_names as $field_name) {?>
				<?php if($field_name == 'gameuid' || $field_name == 'data_id') continue;?>
				<tr>
					<td><?php echo $field_name;?></td>
					<td>
						<input type="text" name="<?php echo $table_name_tmp;?>_<?php echo $field_name;?>"/>
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
	<?php } ?>
	<?php if(!empty($table_name)) { ?>
	<?php if(!empty($rowsPacked)) { ?>
	<tr id="unpacked_data_grid_wrapper"><td colspan="2" align="center">
		<table border="1">
		<?php if(!empty($extra_info_packed)) { ?>
		<tr><td colspan="<?php echo $column_count_packed;?>"><?php echo $extra_info_packed;?></td></tr>
		<?php } ?>
		<tr>
			<?php foreach($headerPacked as $idx=>$column_name) {?>
			<td><?php echo $column_name;?></td>
			<?php }?>
		</tr>
		<?php if(is_array($rowsPacked) && count($rowsPacked) > 0) { ?>
		<?php foreach($rowsPacked as $row) {?>
		<tr id="unpacked_data_grid">
			<?php foreach($row as $idx=>$column_value) {?>
			<td><?php echo $column_value;?></td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php } ?>
		</table>
	</td></tr>
	<?php } ?>
	<tr id="data_grid_wrapper"><td colspan="2" align="center">
		<table border="1">
		<tr>
			<?php foreach($header as $idx=>$column_name) {?>
			<td><?php echo $column_name;?></td>
			<?php }?>
			<td>删除&nbsp;&nbsp;修改</td>
		</tr>
		<?php if(is_array($rows) && count($rows) > 0) { ?>
		<?php $primery_fields_name=_get_primery_key_field($table_name);?>
		<?php foreach($rows as $row) {?>
		<tr id="data_grid">
			<?php foreach($header as $idx) {?>
			<?php $id_value=_get_id_value($table_name,$row);$column_value=$row[$idx];?>
			<?php if(is_array($column_value)) { ?>
			<?php $column_value=implode_string($column_value);?>
			<?php } ?>
			<td id="<?php echo $table_name;?>_<?php echo $id_value;?>_<?php echo $idx;?>"><?php echo $column_value;?></td>
			<?php if(!in_array($idx,$primery_fields_name)) { ?>
			<td id="<?php echo $table_name;?>_<?php echo $id_value;?>_<?php echo $idx;?>_modify" style="display:none">
				<?php if(is_array($modify_index[$table_name][$idx])) { ?>
				<select name="<?php echo $table_name;?>_<?php echo $id_value;?>_<?php echo $idx;?>">
					<?php foreach($modify_index[$table_name][$idx] as $id=>$desc) {?>
					<?php if($id == $column_value) { ?>
					<option value="<?php echo $id;?>" selected="selected"><?php echo $desc;?></option>
					<?php } else { ?>
					<option value="<?php echo $id;?>"><?php echo $desc;?></option>
					<?php } ?>
					<?php }?>
				</select>
				<?php } else { ?>
				<input type="text" name="<?php echo $table_name;?>_<?php echo $id_value;?>_<?php echo $idx;?>" value="<?php echo $column_value;?>"/>
				<?php } ?>
			</td>
			<?php } ?>
			<?php } ?>
			<td><input type="radio" value="delete" id="<?php echo $table_name;?>_<?php echo $id_value;?>_delete" name="<?php echo $table_name;?>_<?php echo $id_value;?>" onclick="confirm_delete('<?php echo $table_name;?>', '<?php echo $id_value;?>')"/>&nbsp;&nbsp;<input id="<?php echo $table_name;?>_<?php echo $id_value;?>_modify" type="radio" value="modify" name="<?php echo $table_name;?>_<?php echo $id_value;?>" onclick="set_modify('<?php echo $table_name;?>', '<?php echo $id_value;?>')"/></td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr><td colspan="{<?php echo $column_count_packed;?>+1}">该用户在表<?php echo $table_name;?>中没有数据</td></tr>
		<?php } ?>
		</table>
	</td></tr>
	
	<?php } ?>
</table>
<table>
	<tr>
		<td colspan="2">
			<input type="submit" value="获取数据" name="get"/>
			<?php if($limitvalue==1) { ?>
				<input type="submit" value="添加数据" name="insert" onclick="return ensure_insert_form();"/>
			<?php } ?>
			<?php if($limitvalue==1 || $limitvalue==2) { ?>
				<input type="submit" value="删除修改" name="modify" onclick="return ensure_modify_form();"/>
			<?php } ?>
		</td>
	</tr>
</table>
</form>
<script language="javascript">
var has_modify = {};
function confirm_delete(table_name, id) {
	if(!confirm('你确认要删除数据'+table_name+'_'+id+'吗？')){
		document.getElementById(table_name+'_'+id).checked = false;
		has_modify[table_name+'_'+id] = false;
	} else {
		has_modify[table_name+'_'+id] = true;
	}
	var td_list = document.getElementsByTagName("td");
	var pattern = new RegExp(table_name+"_"+id+"_"+'\\w+'+"_modify");
	for (var idx in td_list) {
		var modify_column = td_list[idx];
		if (!modify_column.id) continue;
		if (modify_column.id.match(pattern)) {
			var column = document.getElementById(modify_column.id.substring(0, modify_column.id.indexOf('_modify')));
			column.style.display="";
			modify_column.style.display="none";
		}
	}
}
function set_modify(table_name, id) {
	has_modify[table_name+'_'+id] = true;
	var td_list = document.getElementsByTagName("td");
	var pattern = new RegExp(table_name+"_"+id+"_"+'\\w+'+"_modify");
	for (var idx in td_list) {
		var modify_column = td_list[idx];
		if (!modify_column.id) continue;
		if (modify_column.id.match(pattern)) {
			var column = document.getElementById(modify_column.id.substring(0, modify_column.id.indexOf('_modify')));
			column.style.display="none";
			modify_column.style.display="";
		}
	}
	document.getElementById("has_modify").value="true";
}
function ensure_insert_form() {
	var table_name = document.getElementById("table_name").value;
	if (table_name == '') {
		alert("请选择数据库表");
		return false;
	}
	var tr_list = document.getElementsByTagName("tr");
	for (var i in tr_list) {
		var tr = tr_list[i];
		if (!tr.id) continue;
		if (tr.id.indexOf("_insert_info") == -1) continue;
		if (tr.id != table_name+"_insert_info") {
			tr.style.display = "none";
		}
	}
	var insert_form = document.getElementById(table_name+"_insert_info");
	if (insert_form.style.display == "none") {
		insert_form.style.display = "";
		var data_grid_wrapper = document.getElementById("data_grid_wrapper");
		if (data_grid_wrapper) data_grid_wrapper.style.display = "none";
		return false;
	}
	return true;
}
function ensure_modify_form() {
	var table_name = document.getElementById("table_name").value;
	if (table_name == '') {
		alert("请选择数据库表");
		return false;
	}
	var gameuid = document.getElementById("gameuid").value;
	var uid = document.getElementById("uid").value;
	if (gameuid == '' && uid == '') {
		alert("请指定用户uid或者gameuid");
		return false;
	}
	var data_grid_wrapper = document.getElementById("data_grid_wrapper");
	if (!data_grid_wrapper) {
		alert("请先获取数据，然后才能修改");
		return false;
	}
	var data_grid = document.getElementById("data_grid");
	if (!data_grid) {
		alert("没有任何数据，请先添加数据才能修改呢:)");
		return false;
	}
	if (data_grid_wrapper.style.display == "none") {
		data_grid_wrapper.style.display = "";
		var insert_form = document.getElementById(table_name+"_insert_info");
		if (insert_form) insert_form.style.display = "none";
		return false;
	}
	var modify_count = 0;
	for (var i in has_modify) {
		if (has_modify[i] == true) modify_count++;
	}
	if (modify_count == 0) {
		alert("没有任何数据更改，不会提交数据库");
		return false;
	}
	return true;
}
</script>
<?php include renderTemplate('footer'); ?>