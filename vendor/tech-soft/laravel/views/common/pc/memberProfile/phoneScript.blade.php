<script>
    $(function () {
        new window.api.commonVerify({
            generateServer: '/member/profile_phone_verify',
            selectorTarget: 'input[name=phone]',
            selectorGenerate: '[data-verify-generate]',
            selectorCountdown: '[data-verify-countdown]',
            selectorRegenerate: '[data-verify-regenerate]',
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'img[data-captcha]',
            interval: 60,
        },window.api.dialog);
    });
</script>