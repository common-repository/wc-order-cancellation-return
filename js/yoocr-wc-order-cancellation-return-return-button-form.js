jQuery(document).ready(function($) {
    
    $(document).on('click', '.return_order_button', function(event) {
        event.preventDefault();
        var orderId = $(this).data('order-id');
        
        $('#returnOrderPopup').show();
        $('#returnOrderPopupOrderId').val(orderId);
        $('.error-message').hide();
    });

    function closeReturnOrderPopup() {
        $('#returnOrderPopup').hide();
    }

    $(document).on('click', '.return-order', closeReturnOrderPopup);

    $(document).on('click', '#submitReturnReasonButton', function() {
        var orderId = $('#returnOrderPopupOrderId').val();
        var reason = $('input[name="return_reason"]:checked').val();
        var otherReason = $('#returnOtherReasonText').val().trim();

        if (!reason) {
            $('#returnError').show();
            return;
        }

        if (reason === "Other" && otherReason === "") {
            $('#returnOtherReasonError').show();
            return;
        }

        var submitButton = $(this);
        submitButton.prop('disabled', true).text(wcocr_return_order_vars.sending_text);

        var nonce = wcocr_return_order_vars.nonce;

        var data = {
            'action': 'handle_order_return',
            'order_id': orderId,
            'reason': reason === "Other" ? otherReason : reason,
            'nonce': nonce
        };

        $.post(wcocr_return_order_vars.ajax_url, data, function(response) {
            closeReturnOrderPopup();
            location.reload();
        }).fail(function() {
            alert('Failed to send the request.');
            submitButton.prop('disabled', false).text('Confirm return');
        });
    });

    $('input[name="return_reason"]').change(function() {
        var selectedValue = $(this).val();
        $('#returnOtherReasonText').toggle(selectedValue === 'Other');
        $('.error-message').hide();
    });
});