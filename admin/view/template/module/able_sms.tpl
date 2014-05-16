<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><?php echo $entry_sms_api_link; ?></td>
          <td><input type="text" size="80" name="able_sms_api_link" value="<?php echo $able_sms_api_link; ?>"></td>
        </tr>
        <tr>
          <td><?php echo $entry_username; ?></td>
          <td><input type="text" name="able_sms_username" value="<?php echo $able_sms_username; ?>"></td>
        </tr>
        <tr>
          <td><?php echo $entry_password; ?></td>
          <td><input type="password" name="able_sms_password" value="<?php echo $able_sms_password; ?>"></td>
        </tr>
      </table>
    </form>
    <h2>SMS Balance</h2>
    <table class="form">
      <tr>
        <td><?php echo $text_current_balance; ?></td>
        <td><?php echo $current_balance; ?></td>
      </tr>
      <tr>
        <td><?php echo $text_via_able_web; ?> </td>
        <td><?php echo $via_able_web; ?></td>
      </tr>
      <tr>
        <td><?php echo $text_via_chaktak; ?></td>
        <td><?php echo $via_chaktak; ?></td>
      </tr>
    </table>
  </div>
</div>

<?php echo $footer; ?>