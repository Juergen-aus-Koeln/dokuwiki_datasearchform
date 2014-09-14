jQuery(function () {
    jQuery('.datasearchform__qsearch_in')
        .each(function (i, input) {
            var $input = jQuery(input);
            var $form = $input.parent().parent();
            var $output = $form.find('.datasearchform__qsearch_out');
            var $ns = $form.find('.datasearchform__ns');

            $input.dw_qsearch({

                output: $output,

                getSearchterm: function () {
                    var query = $input.val(),
                        reg = new RegExp("(?:^| )(?:@|ns:)[\\w:]+");

                    if (reg.test(query)) {
                        return query;
                    } else {
                        var namespace = $ns.val();
                        return query + (namespace ? ' @' + namespace : '');

                    }
                }
            });

        });
});