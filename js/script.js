
/*$(document).on('click','#list_li_1', function () {
	$.ajax({type: "POST",url: "ajax/database",data: "view=list&action=catalog-style",dataType: "html",cache: false,success: function (data) {
			$(".ajax_input").html(data);
			$(".toolkit p").text("Таблица");
			$("title").text("Таблица");
		}});
});*/

$(document).on('click','.logon_left, .logon', function () {
	$.ajax({
		type: "POST", url: "logon", data: "", dataType: "html", cache: false, success: function (data) {
			$(".ajax_input").html(data);
			$(".toolkit p").text("Админ-панель");
			$("title").text("Админ-панель");
			$(document).on('click','#update', function () {
				$(".capcha").replaceWith('<img src = "/captcha.php" class="capcha" width="120" height="40"/>');
			});














		}
	});
});

$(function() {

	$(".menu_slide").on('click', function() {
		$(".sidebar").toggleClass("sidebar_width_0");
		$(".logon_left").toggleClass("sidebar_click_opacity");
		$(".sidebar").toggle();
		$(".center").toggleClass("center_ml_100");
	});


	$('.form-data-auth').submit(function (e) {
		//$(".alert-danger,.alert-success").hide();
		var form_data = $(this).serialize();
		$.ajax({type: "POST",url: "/ajax/profile",data: form_data+"&action=auth",dataType: "html",cache: false,
			success: function (data) {
				if(data == true){
					location.href = "/";
				}else{
					$('.alert-danger').html(data).show();
				}
			}
		});
		e.preventDefault();
	});
	$('.form-data-profile-add').submit(function (e) {
		var form_data = $(this).serialize();
		$.ajax({type: "POST",url: "/ajax/profile",data: form_data+"&action=add",dataType: "html",cache: false,
			success: function (data) {
				if(data == true){
					location.href = "?tab=users";
				}else{
					$('.alert-danger').html(data).show();
				}
			}
		});
		e.preventDefault();
	});
	$('.form-data-profile').submit(function (e) {
		var form_data = $(this).serialize();
		$('.proccess_load').show();
		$.ajax({type: "POST",url: "/ajax/profile",data: form_data+"&action=update",dataType: "html",cache: false,
			success: function (data) {
				if(data == true){
					$('.alert-success').html("Сохранено!").show();
					$('.alert-danger').hide();
					$('.proccess_load').hide();
				}else{
					$('.alert-danger').html(data).show();
					$('.alert-success').hide();
					$('.proccess_load').hide();
				}
			}
		});
		e.preventDefault();
	});
	$('.form-data-settings-save').submit(function (e) {
		var form_data = $(this).serialize();
		$('.proccess_load').show();
		$.ajax({type: "POST",url: "/ajax/admin",data: form_data+"&action=settings-save",dataType: "html",cache: false,
			success: function (data) {
				if(data == true){
					$('.alert-success').html("Настройки сохранены успешно!").show();
					$('.alert-danger').hide();
					$('.proccess_load').hide();
				}else{
					$('.alert-danger').html(data).show();
					$('.alert-success').hide();
					$('.proccess_load').hide();
				}
			}
		});
		e.preventDefault();
	});



	const swalWithBootstrapButtons = Swal.mixin({
		customClass: {
			confirmButton: 'btn btn-success',
			cancelButton: 'btn btn-danger'
		},
		buttonsStyling: false
	});



	$(document).on('click','.change-status-client', function () {

		var uid = $(this).attr("data-id");
		var status = $(this).attr("data-status");

		if(status == 2){
			swalWithBootstrapButtons.fire({
				title: 'Вы действительно хотите заблокировать пользователя?',
				text: "Пользователь будет заблокирован!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Да',
				cancelButtonText: 'Нет',
				reverseButtons: true
			}).then((result) => {
				if (result.value) {

					$('.proccess_load').show();
					$.ajax({
						type: "POST",url: "ajax/admin",data: "action=user-upload-status&id="+uid+"&status="+status,dataType: "html",cache: false,
						success: function (data) {
							if(data == true){
								location.reload();
							}else{
								$('.proccess_load').hide();
								swalWithBootstrapButtons.fire(
									'Ошибка',
									data,
									'error'
								)
								//notification();
							}
						}
					});

				}
			});

		}else{

			$('.proccess_load').show();
			$.ajax({
				type: "POST",url: "ajax/admin",data: "action=user-upload-status&id="+uid+"&status="+status,dataType: "html",cache: false,
				success: function (data) {
					if(data == true){
						location.reload();
					}else{
						$('.proccess_load').hide();
						swalWithBootstrapButtons.fire(
							'Ошибка',
							data,
							'error'
						)
					}
				}
			});

		}

		return false;
	});
	$(document).on('click','.change-type-client', function () {

		var uid = $(this).attr("data-id");
		var data_type = $(this).attr("data-type");
		$('.proccess_load').show();
		$.ajax({
			type: "POST",url: "ajax/admin",data: "action=user-upload-type-person&id="+uid+"&type="+data_type,dataType: "html",cache: false,
			success: function (data) {
				if(data == true){
					location.reload();
				}else{
					$('.proccess_load').hide();
					swalWithBootstrapButtons.fire(
						'Ошибка',
						data,
						'error'
					)
					//notification();
				}
			}
		});


		return false;
	});
	$(document).on('click','.delete', function () {

		var uid = $(this).attr("data-id");
		swalWithBootstrapButtons.fire({
			title: 'Вы действительно хотите удалить пользователя?',
			text: "Пользователь будет удален!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Да',
			cancelButtonText: 'Нет',
			reverseButtons: true
		}).then((result) => {
			if (result.value) {

				$('.proccess_load').show();
				$.ajax({
					type: "POST",url: "ajax/admin",data: "action=user-delete&id="+uid,dataType: "html",cache: false,
					success: function (data) {
						if(data == true){
							location.reload();
						}else{
							$('.proccess_load').hide();
							swalWithBootstrapButtons.fire(
								'Ошибка',
								data,
								'error'
							)
							//notification();
						}
					}
				});

			}
		});

		return false;
	});
	$('#text_message').on('textarea keyup', function(e) {
		$("#leng_lest").text("Осталось символов "+((69*2)-$('#text_message').val().length));
	});


	$(document).on('click','.icon_table_message', function () {
		var uid = $(this).attr("data-id");
		Swal.fire({
			title: 'Отправить сообщение',
			input: 'text',
			inputAttributes: {
				autocapitalize: 'off'
			},
			text: "Пример: "+$("#text_message_send").val(),
			showCancelButton: true,
			confirmButtonText: 'Отправить',
			showLoaderOnConfirm: true,
					preConfirm: (text) => {
						return $.ajax({
							type: "POST",url: "ajax/admin",data: "action=info&id="+uid+"&text="+text,dataType: "html",cache: false,
							success: function (data) {
									return data;
							}
						});
                    },
			allowOutsideClick: () => !Swal.isLoading()
		}).then((result) => {
			var res = JSON.parse(result.value);
			console.log(res.status);
			console.log(res['status']);
			if (res.status) {
				Swal.fire({
					title: "Сообщение отправлено",
					imageUrl: "https://hoster.kz/pic/hoster_logo_old.png"
				});
			}else{
				swalWithBootstrapButtons.fire(
					'Ошибка',
					res.error_text,
					'error'
				);
			}
		});
	});


/*	$('#search_domais').on('keyup', function(e) {
		var text = $(this).val();
		console.log(text);
		return $.ajax({
			type: "POST",url: "ajax/admin",data: "action=info&id="+uid+"&text="+text,dataType: "html",cache: false,
			success: function (data) {
				return data;
			}
		});
	});*/
});









/*
$(".menu_slide").click(function(){
	width = $(".sidebar").width();
	if(width == 220){
		$(".sidebar").animate({
			width:'0',
			opacity:'0'
		});
		$(".center").animate({
			marginLeft:'0'
		});
	}else{
		$(".sidebar").animate({
			width:'220',
			opacity:'1'
		});
		$(".center").animate({
			marginLeft:'220'
		});
	}

});*/
