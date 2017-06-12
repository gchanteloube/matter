
Payway = function(){};
Payway.prototype = {
    eventListeners: function () {
        var This = this;

        var $form = $('#payment-form');
        $form.submit(function() {
            $form.find('.label-pay-button').hide();
            $form.find('.loader-pay-button').show();
            $form.find('input').prop('disabled', true);
            Stripe.card.createToken($form, This.stripeResponseHandler);
            return false;
        });

        $('.paypal-logo').click(function () {
            $('.paypal-logo').hide();
            $('.paypal-module').show();
        });
    },

    stripeResponseHandler: function (status, response) {
        var $form = $('#payment-form');

        if (response.error) {
            $form.find('.payment-errors').text(response.error.message);
            $form.find('input').prop('disabled', false);
            $form.find('.label-pay-button').show();
            $form.find('.loader-pay-button').hide();
        } else {
            var token = response.id;
            $form.find('input').prop('disabled', false);
            $form.append($('<input type="hidden" name="stripeToken">').val(token));
            var serializable = $form.serialize();
            $form.find('input').prop('disabled', true);

            var payment = $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: serializable,
                async: false
            }).responseText;

            if (payment != null && payment != undefined) {
                var confirm = $.parseJSON(payment);
                if (confirm == 'Your payment was done, thank you.') document.location.href = 'payment.confirm';
                else $form.find('.payment-errors').text(confirm);
            }
            $form.find('input').prop('disabled', false);
            $form.find('input').val('');
            $form.find('.label-pay-button').show();
            $form.find('.loader-pay-button').hide();
        }
    },

    init: function () {
        var This = this;

        this.eventListeners();

        var card = new Card({form: '.active form', container: '.card-wrapper'});
    }
};