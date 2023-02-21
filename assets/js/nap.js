(function($) {

    $(document).ready(function(){

        $("<input>").attr({
            name: "nap_ajax_nonce",
            id: "nap_ajax_nonce",
            type: "hidden",
            value: napVar.nonce
        }).appendTo(napVar.form);

    });

})(jQuery);