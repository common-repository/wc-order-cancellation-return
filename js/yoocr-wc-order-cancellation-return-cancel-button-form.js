jQuery(document).ready(function($) {
    
    $(document).on('click', '.cancel_order_button', function(event) {
        event.preventDefault();
        var orderId = $(this).data('order-id');
        console.log('Order ID:', orderId);  // Log the order ID
        
        $('#cancelOrderPopup').show();
        $('#cancelOrderPopupOrderId').val(orderId);
        $('.error-message').hide();
    });

    function closeCancelOrderPopup() {
        console.log('Closing cancel order popup.');
        $('#cancelOrderPopup').hide();
    }

    $(document).on('click', '.cancel-order', closeCancelOrderPopup);

    $(document).on('click', '#submitCancelReasonButton', function() {
        var orderId = $('#cancelOrderPopupOrderId').val();
        var reason = $('input[name="cancel_reason"]:checked').val();
        var otherReason = $('#cancelOtherReasonText').val().trim();

        console.log('Order ID for cancellation:', orderId);
        console.log('Selected reason:', reason);
        console.log('Other reason (if any):', otherReason);

        if (!reason) {
            $('#cancelError').show();
            console.log('No reason selected for cancellation.');
            return;
        }

        if (reason === "Other" && otherReason === "") {
            $('#cancelOtherReasonError').show();
            console.log('Other reason selected but not provided.');
            return;
        }

        var submitButton = $(this);
        submitButton.prop('disabled', true).text(wcocr_cancel_order_vars.sending_text);

        var data = {
            'action': 'handle_order_cancellation',
            'order_id': orderId,
            'reason': reason === "Other" ? otherReason : reason,
            'nonce': wcocr_cancel_order_vars.nonce
        };

        console.log('Sending data:', data);

        $.post(wcocr_cancel_order_vars.ajax_url, data, function(response) {
            console.log('Response:', response);
            closeCancelOrderPopup();
            location.reload();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX request failed:', textStatus, errorThrown);
            alert('Failed to send the request.');
            submitButton.prop('disabled', false).text('Confirm cancellation');
        });
    });

    $('input[name="cancel_reason"]').change(function() {
        var selectedValue = $(this).val();
        $('#cancelOtherReasonText').toggle(selectedValue === 'Other');
        $('.error-message').hide();
        console.log('Reason changed to:', selectedValue);
    });
});
