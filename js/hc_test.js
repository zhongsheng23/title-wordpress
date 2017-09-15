jQuery(document).ready( function($) {

    // 保存设置
	$('#zs_save').click( function() {
      
        // radion是否选中
        var val=$('input:radio[name="zs_weizhi"]:checked').val();
        if(val==null){
            radio = false;
            return;
        }else{
            radio = val
        }

		$.ajax({
			type: "POST",
			data: "zs_username=" + $('#zs_username').val() + "&zs_title=" + $('#zs_title').val() + "&zs_color=" + $('#zs_color').val() + "&radio=" +radio+"&zs_ico=" + escape($('#zs_ico').val())  + "&action=zs_touxian",
			url: ajax_object.ajax_url,
			beforeSend: function() {
				$('#error_color').html('save......'); 
			},
			success: function( $data ) {
				if( $data == 'ok'){
				    $('#error_color').html('<div style="font-size:20px" id="message" class="updated">&#x8BBE;&#x7F6E;&#x6210;&#x529F;</div>');

                    $.ajax({
                        type: "POST",
                        data: "action=zs_reset",
                        url: ajax_object.ajax_url,
                        // beforeSend: function() {
                            // $('#error_color').html('reset......'); 
                        // },
                        success: function( $data ) {
                            jQuery('.widefat tbody').empty();
                            jQuery('.widefat tbody').append($data);

                        }
                    });

          
				} else {
				    $('#error_color').html('<div style="font-size:20px" id="message" class="error">&#x51FA;&#x73B0;&#x9519;&#x8BEF;</div>'); 
				}
			}
		});
	});
    // 删除

});
