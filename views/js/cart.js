/*https://webkul.com/blog/get-action-change-product-combination-product-page-front-end/*/
$(document).ready(function() {
	var ajax_url = $('#cart-donation-amount').data('donationlink');
	if (typeof prestashop !== 'undefined') {
	  prestashop.on(
	    'updateCart',
	    function (event) {
	      var eventDatas = {};
		    $.ajax({
		        type: 'POST',
		        url: ajax_url,
		        cache: false,
		        dataType: 'JSON',
		        data: {
		            action: 'CartDonation',
		            ajax: true
		        },
		        success: function(data) {
		        	console.log(data);
		            if (data.return) {
		            	$('.donation-block').show();
		            	$('#donation-amount, #cart-donation-amount').html( data.donation );
		            } else {
		            	$('.donation-block').slideUp();
		            }
		        },
		        error: function(jqXHR, textStatus, errorThrown) {
		            $('.donation-block').slideUp();
		            console.log(textStatus + ' ' + errorThrown);
		        }
		    });
	    }
	  );
	}
});