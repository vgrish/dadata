/** v 1.0.0 */

if (typeof(modxDaData) == 'undefined') {
    modxDaData = {
        Init: false
    };
}

modxDaData = {
    initialize: function (key) {

        if (!jQuery().suggestions) {
            document.write('<script src="' + modxDaDataConfig.assetsUrl + 'vendor/dadata/js/jquery.suggestions.min.js"><\/script>');
        }

        $(document).ready(function () {
            $.each(key, function (i, item) {

                var selector = item['selector'];
                if (!!!selector) {
                    return true;
                }

                var $parent = $(selector);
                if (!!!$parent) {
                    return true;
                }

                modxDaData.suggestions.initialize($parent, item);

            });
        });

        modxDaData.Init = true;

        $(document).bind('DaData_onSelect', function (e, suggestion, changed, item, input, config) {

            var data = suggestion.data;
            if (!data) {
                return;
            }
            var parent = $(item.selector);
            if (!!!parent) {
                return true;
            }

            /** master **/
            var master = config.master;
            if (!!master) {
                $.each(master, function (name, key) {
                    var $input = parent.find('[name="' + name + '"]');
                    if (!!!$input) {
                        return true;
                    }

                    var movalue = $input.val();
                    var mnvalue = '';

                    var ovalue = input.attr('data-oldvalue');
                    var nvalue = data[key.toLowerCase()];

                    switch (true) {
                        case movalue == '':
                            mnvalue = nvalue;
                            break;
                        case movalue != '' && (ovalue == '' || !ovalue):
                            mnvalue = movalue + ' ' + nvalue;
                            break;
                        case movalue != '' && ovalue != '':
                            mnvalue = movalue.replace(ovalue, nvalue);
                            break;
                    }

                    modxDaData.suggestions.setvalue($input, mnvalue);
                });
            }

            /** subject **/
            var subject = config.subject;
            if (!!subject) {
                $.each(subject, function (name, key) {
                    var $input = parent.find('[name="' + name + '"]');
                    if (!!!$input) {
                        return true;
                    }
                    var ovalue = $input.val();
                    var nvalue = data[key.toLowerCase()];
                    modxDaData.suggestions.setvalue($input, nvalue);

                    /** gender **/
                    if (!!data.gender) {
                        $input.suggestions('setOptions', {params: {gender: data.gender}});
                    }
                });
            }

            modxDaData.suggestions.setvalue(input, suggestion.value);

            return true;
        });

    }
};

modxDaData.suggestions = {

    initialize: function (parent, item) {
        if (!!!item.idx) {
            console.log('[dadata] Error not idx');
        }
        var suggestions = item.suggestions;
        if (!!suggestions && suggestions != '{}') {

        }

        $.each(suggestions, function (name, config) {

            var $input = parent.find('[name="' + name + '"]');
            if (!!!$input) {
                return true;
            }

            /*
             config https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093361
             callbacks https://confluence.hflabs.ru/pages/viewpage.action?pageId=207454320
             */
            $input.suggestions($.extend({}, {
                serviceUrl: modxDaDataConfig.restUrl,
                token: item.propkey,
                noCache: true,
                beforeRender: function (container) {
                    $(document).trigger('DaData_beforeRender', [container, item, $input, config]);
                },
                formatSelected: function (suggestion) {
                    $(document).trigger('DaData_formatSelected', [suggestion, item, $input, config]);
                },
                onInvalidateSelection: function (suggestion) {
                    $(document).trigger('DaData_onInvalidateSelection', [suggestion, item, $input, config]);
                },
                onSearchStart: function (query) {
                    $(document).trigger('DaData_onSearchStart', [query, item, $input, config]);
                },
                onSearchComplete: function (query, suggestions) {
                    $(document).trigger('DaData_onSearchComplete', [query, suggestions, item, $input, config]);
                },
                onSearchError: function (query, jqXHR, textStatus, errorThrown) {
                    $(document).trigger('DaData_onSearchError', [query, jqXHR, textStatus, errorThrown, item, $input, config]);
                },
                onSelect: function (suggestion, changed) {
                    $(document).trigger('DaData_onSelect', [suggestion, changed, item, $input, config]);
                },
                onSelectNothing: function (query) {
                    $(document).trigger('DaData_onSelectNothing', [query, item, $input, config]);
                }
            }, config));

        });
    },

    setvalue: function (input, value) {
        input.val(value).data('oldvalue', '').attr('data-oldvalue', value).trigger('change');
        return true;
    }

};
