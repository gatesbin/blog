<script>
    $(function () {
        new window.api.commonVerify({
            generateServer: '/retrieve/email_verify',
            selectorTarget: 'input[name=email]',
            selectorGenerate: '[data-email-verify-generate]',
            selectorCountdown: '[data-email-verify-countdown]',
            selectorRegenerate: '[data-email-verify-regenerate]',
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'img[data-captcha]',
            interval: 60,
        },window.api.dialog);
    });
</script>