/*https://webkul.com/blog/get-action-change-product-combination-product-page-front-end/*/
$(document).ready(function() {
    var ajax_url = $('#cart-donation-amount').data('donationlink');
    if (typeof prestashop !== 'undefined') {
      prestashop.on(
        'updatedProduct',
        function (event) {
          var eventDatas = {};
          console.log(event);
            // $.ajax({
            //     type: 'POST',
            //     url: ajax_url,
            //     cache: false,
            //     dataType: 'JSON',
            //     data: {
            //         action: 'CartDonation',
            //         ajax: true
            //     },
            //     success: function(data) {
            //         console.log(data);
            //         if (data.return) {
            //             $('#cart-donation-amount').html( data.donation );
            //         } else {
            //             $('#cart-donation-amount').hide();
            //         }
            //     },
            //     error: function(jqXHR, textStatus, errorThrown) {
            //         console.log(textStatus + ' ' + errorThrown);
            //     }
            // });
        }
      );
    }
});