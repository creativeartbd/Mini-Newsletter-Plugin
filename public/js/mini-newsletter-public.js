;(function ($) {
    $(document).ready(function () {        
        $("#mn_submit").on('click', function (e) {
            var mn_email = $("#mn_email").val();
            var nonce = $("#mn_nonce_field").val();

            $.ajax({
				type 		: 	'POST',
				url 		: 	urls.ajaxurl,
				dataType 	: 	'html',
				data 		: 	{
					action 		: 	"mn_action",
                	mn_email 	: 	mn_email,
                	s 			: 	nonce
				},
				beforeSend : function () {
					$("#mn_submit").html('wait...');
				},
				success: function ( result ) {
					$("#mn_submit").html('<i class="icofont icofont-paper-plane"></i>');					
					$("#mn_form_result").html( result );
				}
				
            });          

            return false;
        });
    });
})(jQuery);