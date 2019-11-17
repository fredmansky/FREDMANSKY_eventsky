(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.TicketTypeSelector = Garnish.Base.extend(
        {
            $typeSelectLinks: null,

            init: function() {
                this.$typeSelectLinks = $('.js-ticketTypeLink');
                // this.$typeSelectLinks = $('a');
                console.log('this.$typeSelectLinks', this.$typeSelectLinks);
                // this.$spinner = $('<div class="spinner hidden"/>').insertAfter(this.$typeSelect.parent());
                this.$typeSelectLinks.forEach(link => {
                    this.addListener(link, 'click', (evt) => {
                        this.onTypeChange(evt, 'test');
                    });
                });
            },

            onTypeChange: function(evt, ticketTypeHandle) {
                console.log('ticketTypeHandle', ticketTypeHandle);
                // this.$spinner.removeClass('hidden');
                //
                // Craft.postActionRequest('eventsky/events/switch-event-type', Craft.cp.$primaryForm.serialize(), $.proxy(function(response, textStatus) {
                //     this.$spinner.addClass('hidden');
                //
                //     if (textStatus === 'success') {
                //         this.trigger('beforeTypeChange');
                //
                //         var $tabs = $('#tabs');
                //         if ($tabs.length) {
                //             $tabs.replaceWith(response.tabsHtml);
                //         } else {
                //             $(response.tabsHtml).insertBefore($('#content'))
                //         }
                //
                //         $('#fields').html(response.fieldsHtml);
                //         Craft.initUiElements($('#fields'));
                //         Craft.appendHeadHtml(response.headHtml);
                //         Craft.appendFootHtml(response.bodyHtml);
                //
                //         // Update the slug generator with the new title input
                //         if (typeof slugGenerator !== 'undefined') {
                //             slugGenerator.setNewSource('#title');
                //         }
                //
                //         Craft.cp.initTabs();
                //
                //         this.trigger('typeChange');
                //     }
                // }, this));
            }
        });
})(jQuery);
