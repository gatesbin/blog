<script>
    $(function () {
        new window.api.commonVerify({
            generateServer: '/retrieve/phone_verify',
            selectorTarget: 'input[name=phone]',
            selectorGenerate: '[data-phone-verify-generate]',
            selectorCountdown: '[data-phone-verify-countdown]',
            selectorRegenerate: '[data-phone-verify-regenerate]',
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'img[data-captcha]',
            interval: 60,
        },window.api.dialog);
    });
</script>