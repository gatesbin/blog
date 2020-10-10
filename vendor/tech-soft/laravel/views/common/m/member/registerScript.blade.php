<script>
    function doCheckCaptcha(){
        $('[data-captcha-status]').hide().filter('[data-captcha-status=loading]').show()
        window.api.base.post('/register/captcha_verify',{captcha:$('[name=captcha]').val()},function (res) {
            window.api.base.defaultFormCallback(res,{
                success:function (res) {
                    $('[data-captcha-status]').hide().filter('[data-captcha-status=success]').show();
                },
                error:function (res) {
                    $('[data-captcha-status]').hide().filter('[data-captcha-status=error]').show();
                    $('[data-captcha]').click();
                }
            })
        })
    }
    $(function () {
        new window.api.commonVerify({
            generateServer: '/register/email_verify',
            selectorTarget: 'input[name=email]',
            selectorGenerate: '[data-email-verify-generate]',
            selectorCountdown: '[data-email-verify-countdown]',
            selectorRegenerate: '[data-email-verify-regenerate]',
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'[data-none]',
            interval: 60,
        },window.api.dialog);
        new window.api.commonVerify({
            generateServer: '/register/phone_verify',
            selectorTarget: 'input[name=phone]',
            selectorGenerate: '[data-phone-verify-generate]',
            selectorCountdown: '[data-phone-verify-countdown]',
            selectorRegenerate: '[data-phone-verify-regenerate]',
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'[data-none]',
            interval: 60,
        },window.api.dialog);
    });
</script>