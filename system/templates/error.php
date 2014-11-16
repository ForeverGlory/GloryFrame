<script language="javascript">
alertMsg.error('<?=$error?$error:"访问出错"?>');
<?php if(param::$isdialog){?>
setTimeout(function(){$.pdialog.closeCurrent();}, 100);
<?php }else{ ?>
setTimeout(function(){navTab.closeCurrentTab();}, 100);
<?php } ?>
</script>
<?php exit();?>