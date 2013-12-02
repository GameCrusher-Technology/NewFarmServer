<table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td><a href="admincp.php?mod=admin&act=index">[管理首页]</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="admincp.php?mod=admin&act=quit">[安全退出]</a></td>
  </tr>
  <?php foreach($menu as $mkey=>$menu_item) {?>
  
  <tr>
    <td><table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#000000">
      <tr>
        <td class="title" onClick="return changemenu('<?php echo $mkey;?>');"><?php echo $menu_item['name'];?></td>
      </tr>
    </table>
      <span id="<?php echo $mkey;?>" >
	  <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC">
      <?php foreach($menu_item['sub_menu'] as $sub_menu) {?>
      <tr>
        <td bgcolor="#FFFFFF"><a href="<?php echo $sub_menu['href'];?>" <?php if(isset($sub_menu['target'])) { ?>target="<?php echo $sub_menu['target'];?>"<?php } ?>><?php echo $sub_menu['name'];?></a></td>
      </tr>
	  <?php }?>
    </table>
	</span>	</td>
  </tr>  
  <?php } ?>
</table>
