
Payway = function(){};
Payway.prototype = {
    eventListeners: function () {
        var This = this;

        var $form = $('#payment-form');
        $form.submit(function() {
            $form.find('.submit').prop('disabled', true);
            Stripe.card.createToken($form, This.stripeResponseHandler);
            return false;
        });
    },

    stripeResponseHandler: function (status, response) {
        var $form = $('#payment-form');

        if (response.error) {
            $form.find('.payment-errors').text(response.error.message);
            $form.find('.submit').prop('disabled', false);
        } else {
            var token = response.id;
            $form.append($('<input type="hidden" name="stripeToken">').val(token));
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: $form.serialize(),
                success: function(html) {
                    alert(html);
                }
            });
        }
    },

    init: function () {
        var This = this;

        this.eventListeners();

        var card = new Card({form: '.active form', container: '.card-wrapper'});
    }
};