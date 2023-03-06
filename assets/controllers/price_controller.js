import { Controller } from 'stimulus';
import $ from 'jquery'
export default class extends Controller {
    connect() {
        $("input[type='checkbox']").change(function() {
            let url = "/get_ingredient_price/" + $(this).val();
            let price = $(".pizza_price").html().replace(',', '.')
            price = parseFloat(price.match(/-?(?:\d+(?:\.\d*)?|\.\d+)/)[0]);

            if(this.checked) {
                $.get(url)
                    .done(function(data) {
                        $(".pizza_price").html((price + data * 1.5).toFixed(2) + ' €');
                    });
            } else {
                $.get(url)
                    .done(function(data) {
                        $(".pizza_price").html((price - data * 1.5).toFixed(2) + ' €');
                    });
            }

        });
    }
}