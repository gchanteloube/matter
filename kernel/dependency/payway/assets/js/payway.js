
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
            $form.append($('<input type="hidden" name="stripeToken">').val(token));

            var payment = $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: $form.serialize(),
                async: false
            }).responseText;

            if (payment != null && payment != undefined) {
                $form.find('.payment-errors').text($.parseJSON(payment));
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