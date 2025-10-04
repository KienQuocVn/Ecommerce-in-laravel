$(document).ready(function(){
    
    (function($) {
        "use strict";

    
    jQuery.validator.addMethod('answercheck', function (value, element) {
        return this.optional(element) || /^\bcat\b$/.test(value)
    }, "type the correct answer -_-");

    // validate contactForm form
    $(function() {
        $('#contactForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                subject: {
                    required: true,
                    minlength: 4
                },
                phone: {
                    required: true,
                    minlength: 9
                },
                email: {
                    required: true,
                    email: true
                },
                message: {
                    required: true,
                    minlength: 20
                }
            },
            messages: {
                name: {
                    required: "Vui lòng nhập tên của bạn.",
                    minlength: "Tên của bạn phải có ít nhất 2 ký tự"
                },
                subject: {
                    required: "Vui lòng nhập chủ đề của bạn.",
                    minlength: "Chủ đề của bạn phải có ít nhất 4 ký tự"
                },
                number: {
                    required: "Vui lòng nhập số điện thoại của bạn.",
                    minlength: "your Number must have at least 9 characters"
                },
                email: {
                    required: "Vui lòng nhập email của bạn.",
                    email: "Vui lòng nhập đúng định dạng email."
                },
                message: {
                    required: "Bạn phải viết gì đó để gửi mẫu đơn này.",
                    minlength: "Tin nhắn của bạn phải có ít nhất 20 ký tự"
                }
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $(form).ajaxSubmit({
                    type:"POST",
                    data: $(form).serialize(),
                    url: $(form).attr('action'),
                    success: function() {
                        $('#contactForm :input').attr('disabled', 'disabled');
                        $('#contactForm').fadeTo( "slow", 1, function() {
                            $(this).find(':input').attr('disabled', 'disabled');
                            $(this).find('label').css('cursor','default');
                            $('#success').fadeIn()
                            $('.modal').modal('hide');
		                	$('#success').modal('show');
                        })
                    },
                    error: function() {
                        $('#contactForm').fadeTo( "slow", 1, function() {
                            $('#error').fadeIn()
                            $('.modal').modal('hide');
		                	$('#error').modal('show');
                        })
                    }
                })
            }
        })
    })
        
 })(jQuery)
})