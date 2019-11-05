(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.TicketTypeSwitcher = Garnish.Base.extend(
        {
            $typeSelect: null,
            $spinner: null,

            init: function() {
                this.$typeSelect = $('#typeId');
                this.$spinner = $('<div class="spinner hidden"/>').insertAfter(this.$typeSelect.parent());

                this.addListener(this.$typeSelect, 'change', 'onTypeChange');
            },

            onTypeChange: function(ev) {
                this.$spinner.removeClass('hidden');

                console.log('FORM', Craft.cp.$primaryForm.serialize());

                Craft.postActionRequest('eventsky/tickets/switch-ticket-type', Craft.cp.$primaryForm.serialize(), $.proxy(function(response, textStatus) {
                    this.$spinner.addClass('hidden');

                    console.log('ASDF');
                    console.log('response', textStatus);

                    if (textStatus === 'success') {
                        this.trigger('beforeTypeChange');

                        var $tabs = $('#tabs');
                        if ($tabs.length) {
                            $tabs.replaceWith(response.tabsHtml);
                        } else {
                            $(response.tabsHtml).insertBefore($('#content'))
                        }

                        // $('#fields').html(response.fieldsHtml);
                        // Craft.initUiElements($('#fields'));
                        // Craft.appendHeadHtml(response.headHtml);
                        // Craft.appendFootHtml(response.bodyHtml);

                        // Update the slug generator with the new title input
                        if (typeof slugGenerator !== 'undefined') {
                            slugGenerator.setNewSource('#title');
                        }

                        Craft.cp.initTabs();

                        this.trigger('typeChange');
                    }
                }, this));
            }
        });
})(jQuery);
