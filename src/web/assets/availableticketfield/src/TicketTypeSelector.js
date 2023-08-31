(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.TicketTypeSelector = Garnish.Base.extend(
        {
            $typeSelect: null,
            $typeSelectLinks: null,
            $ticketTypeList: null,
            $blockContainer: null,
            $selectButton: null,
            $spinner: null,

            init: function() {
                this.$typeSelect = document.querySelector('#availableTickets-field .buttons');
                this.$typeSelectLinks = document.querySelectorAll('.js-ticketTypeLink');
                this.$ticketTypeList = document.querySelector('.js-ticketTypeList');
                this.$blockContainer = document.querySelector('#availableTickets-field .blocks');
                this.$selectButton = document.querySelector('#availableTickets-field .buttons .menubtn');
                this.$spinner = $('<div class="spinner hidden" style="margin-left: 24px;" />').appendTo(this.$typeSelect);

                this.$typeSelectLinks.forEach(link => {
                    this.addListener(link, 'click', (evt) => {
                        const ticketTypeHandle = evt.currentTarget.dataset['type'];
                        this.onTypeChange(evt, ticketTypeHandle);
                    });
                });
            },

            onTypeChange: function(evt, ticketTypeHandle) {
                this.$spinner.removeClass('hidden');

                Craft.postActionRequest('eventsky/events/add-new-ticket-type', {'ticketType': ticketTypeHandle}, $.proxy(function(response, textStatus) {
                    this.$spinner.addClass('hidden');

                    if (textStatus === 'success') {
                        this.addMappingBlock(response);
                        this.hideBlockTypeFromMenu(evt);

                        if (this.allTicketTypesMapped()) {
                            this.hideAddTicketTypeButton();
                        }
                    }
                }, this));
            },

            addMappingBlock(response) {
                const html = response.fieldHtml;
                const node = $(html)[0];
                const button = node.querySelector('.deleteMappingLink');

                window.eventTicketTypeMappingRemover.initDeleteButton(button);

                this.$blockContainer.append(node);

                Craft.initUiElements($(this.$blockContainer));
                Craft.appendBodyHtml(response.bodyHtml);
            },

            hideBlockTypeFromMenu(evt) {
                $(evt.currentTarget).parent().addClass('hidden');
            },

            hideAddTicketTypeButton() {
                $(this.$selectButton).addClass('hidden');
            },

            allTicketTypesMapped() {
                return this.$ticketTypeList.querySelectorAll('li:not(.hidden)').length === 0;
            }
        });
})(jQuery);
