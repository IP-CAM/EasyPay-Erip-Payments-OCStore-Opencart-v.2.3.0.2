<div><?php echo $redirect;?></div>

<form action="<?php echo $action; ?>" accept-charset="utf-8" method="post" id="payment">
    <input type="hidden" name="EP_Sum" value="<?php echo $amount;   ?>">
    <input type="hidden" name="EP_OrderNo" value="<?php echo $order_id; ?>">
    <input type="hidden" name="EP_MerNo" value="<?php echo $merchant; ?>">
    <input type="hidden" name="EP_Debug" value="<?php echo $debug;    ?>">
    <input type="hidden" name="EP_Expires" value="<?php echo $expires;  ?>">
    <input type="hidden" name="EP_Encoding" value="<?php echo " utf-8"; ?>">
    <input type="hidden" name="EP_Hash" value="<?php echo $hash;     ?>">
    <input type="hidden" name="EP_Success_URL" value="<?php echo $return;   ?>">
    <input type="hidden" name="EP_Cancel_URL" value="<?php echo $fail;     ?>">
    <input type="hidden" name="EP_Comment" value="<?php echo $EP_Comment;?>">
    <input type="hidden" name="EP_OrderInfo" value="<?php echo $EP_OrderInfo;?>">
    <input type="hidden" name="EP_PayType" value="PT_ERIP">
</form>

<script type="text/javascript">
    function formSubmit() {
        document.getElementById("payment").submit();
    }

    window.onload = function () {
        window.setTimeout(formSubmit, 2000);
    };
</script>