<script src="wizard/js/jquery-2.1.1.min.js"></script>
<script src="wizard/js/bootstrap.min.js"></script>
<script src="wizard/js/custom.js"></script>
<!--<script src='wizard/js/jquery.min.js'></script>-->
<script>
jQuery( document ).ready(function() {
	var did = document.getElementById('did').value;//alert('#'+did+' div:first');
	if(did != 'undefined' && did){
		if(did == 'soap'){
			document.getElementById("soap_form").reset();
			var tid = document.getElementById('tid').value;
			var ul = parseInt(document.getElementById('ul').value);

			if(tid == 'e'){
				jQuery('#type').val('e');
				document.getElementById('new').style.display = 'none';
				document.getElementById('existing').style.display = 'block';
				jQuery('#username').val('');
				document.getElementById('name').value = '';
				document.getElementById('email').value = '';
				if(ul == 0){
					$('.confdiv').hide();//document.getElementById('confdiv').style.display = 'none';
					document.getElementById('ulDiv').style.display = 'none';
				}else document.getElementById('spanId').style.display = 'none';
			}
		}
		if(did == 'setdb'){
			 jQuery('#panel0').addClass('in'); jQuery('#panel0').removeClass('collapse');
			// jQuery('.panel-collapse').removeClass('in');
			checkSubmits(1);
		}

		jQuery(".active").removeClass('active');
		jQuery('#'+did+' div:first').addClass('active');
		jQuery('#'+did+' h3:first').addClass('active');
	}else{
		jQuery('.stage-header head-icon','#step1').addClass('active');
	}
	jQuery('#allow').hide();
});

function loadLoader1(aid){
	jQuery('.accordion-toggle').attr("data-toggle", "dropdown");
	jQuery('#myLoader_'+aid).show();
}

function submitData(divid,aid,storeUrl){
	if(divid){
		jQuery("#"+divid).submit(function(e) {
			e.preventDefault();
			var DATA = jQuery(this).serializeArray();
			var len = 0;
			jQuery.each(DATA, function(i, elem) {
				len++;
			});
			if(len>1){
				if(jQuery("#"+divid+'_uname').val().trim()){
					var email = jQuery("#"+divid+'_uname').val().trim();
					var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
					if(!regex.test(email)){
						jQuery('#msgdiv').html('Please provide a valid email for "INKXE LOGIN ID".');
						jQuery('#myModal').modal('show');
						return false;
					}
				}

				var upwd = jQuery("#"+divid+'_upwd').val().trim();
				if(upwd.length == 6 || upwd.length > 6){
					var regex = /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
					if(!regex.test(upwd)){
						jQuery('#msgdiv').html('Please provide valid "INKXE LOGIN PASSWORD".');
						jQuery('#myModal').modal('show');
						return false;
					}else{
						if(jQuery("#"+divid+'_upwd').val().trim() != jQuery("#"+divid+'_cupwd').val().trim()){
							jQuery('#msgdiv').html("These passwords don't match. Try again?");
							jQuery('#myModal').modal('show');
							jQuery("#"+divid+'_cupwd').val('');
							return false;
						}
					}
				}else{
					jQuery('#msgdiv').html('"INKXE LOGIN PASSWORD" should be of minimum 6 characters.');
					jQuery('#myModal').modal('show');
					return false;
				}

				if(jQuery("#"+divid+'_que').val() == 0){
					jQuery('#msgdiv').html('Please provide "SECURITY QUESTION FOR FORGET PASSWORD".');
					jQuery('#myModal').modal('show');
					return false;
				}
				jQuery('#msgdiv').html('');

				var url1 = "db_setup.php";
				loadLoader1(aid);
				jQuery.post( url1,DATA ,function(msg) {//alert('here: '+msg);
					if(msg == 1){
						msg = 'Database information successfully updated for the store <b>'+storeUrl+'</b>.<br />You can click Next to proceed further.';
						jQuery('#msgdiv').html(msg);jQuery('#myModal').modal('show');
						jQuery('#panel'+aid).removeClass('in').addClass('collapse');
						jQuery('#myLoader_'+aid).hide();
						jQuery("input[type=text],[type=password]").val("");
						jQuery('.accordion-toggle').attr("data-toggle", "collapse");
						document.getElementById('next').style.display = 'block';
						if(document.getElementById('total_stores').value == 1)window.location.href = 'index.php?action=finish';
						return false;
					}else if(msg == 2){
						jQuery('#panel'+aid).removeClass('in').addClass('collapse');
						jQuery("input[type=text],[type=password]").val("");
						msg = 'You have already saved the information for the store <b>'+storeUrl+'</b>.<br />You can click Next to proceed further.';
						jQuery('#msgdiv').html(msg);jQuery('#myModal').modal('show');
						jQuery('#myLoader_'+aid).hide();
						jQuery('.accordion-toggle').attr("data-toggle", "collapse");
						//document.getElementById('next').style.display = 'block';
						return false;
					}else{
						jQuery('#msgdiv').html(msg);jQuery('#myLoader_'+aid).hide();jQuery('#myModal').modal('show');
						//document.getElementById('next').style.display = 'none';
						return false;
					}
				});
			}
		});
	}else{
		jQuery('#msgdiv').html('Data has been successfully saved once.');
		jQuery('#myModal').modal('show');
		return false;
	}
}

function checkSubmits(req){
	var url1 = "chk_file.php";
	jQuery.post( url1,{chk:1} ,function(msg) {
		if(msg == 0){
			if(req == 1){
				document.getElementById('next').style.display = 'none';
			}else{
				jQuery('#msgdiv').html('Please fill up atleast for one store.');
				jQuery('#myModal').modal('show');
				return false;
			}
		}else{
			if(req == 1){
				document.getElementById('next').style.display = 'block';
			}else{
				window.location.href = 'index.php?action=finish';
			}
		}
	});
}

function checkCompatible(){
	jQuery('#msgdiv').html('Please make your settings compatible with the recommended settings.');
	jQuery('#myModal').modal('show');
	var bitwiseValue = document.getElementById('bitwiseValue').value;
	if(parseInt(bitwiseValue) < 78 || parseInt(bitwiseValue) == 78){
		jQuery('#compatibility_msg_div').show();
		if(document.getElementById('ap').value == 1){
			jQuery('#disallow').hide();jQuery('#allow').show();
		}else{
			jQuery('#allow').hide();	jQuery('#disallow').show();
			jQuery('#disAllowLink').attr('disabled', 'disabled');
		}
	}else{
		jQuery('#compatibility_msg_div').hide();
	}
}

function setRole(obj){
	var integration_id = obj.value;
	if(integration_id == 0){
		jQuery('#roletd').html('').hide();
	}else{
		document.getElementById('roletd').style.display = 'block';
		var url = 'getRoleList.php?id='+integration_id;
		jQuery.post(url,{id:integration_id},function(res){
			jQuery('#roletd').html(res);
		});
	}
}

function checkType(obj){
	var ul = parseInt(document.getElementById('ul').value);
	var type = obj.value;

	if(type == 'e'){
		document.getElementById('new').style.display = 'none';
		document.getElementById('existing').style.display = 'block';
		document.getElementById('name').value = '';
		document.getElementById('email').value = '';
	}else if(type == 'n'){
		document.getElementById('new').style.display = 'block';
		document.getElementById('existing').style.display = 'none';
		document.getElementById('integration_id').value = 0;
	}
}

function loadLoader(){
	jQuery('#myLoader').show();
}

function validate_soap(){
	var type = jQuery("#soap_form input[type='radio']:checked").val();

	if(type == 'n'){
		if(document.getElementById('name').value == 'undefined' || document.getElementById('name').value == ''){
			jQuery('#msgdiv').html('Please provide "INTEGRATION NAME".');
			jQuery('#myModal').modal('show');
			return false;
		}
		if(document.getElementById('email').value == 'undefined' || document.getElementById('email').value == ''){
			jQuery('#msgdiv').html('Please provide "EMAIL".');
			jQuery('#myModal').modal('show');
			return false;
		}else{
			var email = document.getElementById('email').value;
			var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
			if(!regex.test(email)){
				jQuery('#msgdiv').html('Please provide a valid "EMAIL".');
				jQuery('#myModal').modal('show');
				return false;
			}
		}
	}else if(type == 'e'){
		var ul = document.getElementById('ul').value;
		if(parseInt(ul) == 0){
			jQuery('#msgdiv').html('Please create new Integration.');
			jQuery('#myModal').modal('show');
			return false;
		}else{
			if(document.getElementById('integration_id').value == 'undefined' || document.getElementById('integration_id').value == '0'){
				jQuery('#msgdiv').html('Please "SELECT INTEGRATION".');
				jQuery('#myModal').modal('show');
				return false;
			}
		}
	}
	loadLoader();
}
</script>
