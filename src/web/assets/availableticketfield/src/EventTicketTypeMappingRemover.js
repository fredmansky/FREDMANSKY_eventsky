(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.EventTicketTypeMappingRemover = Garnish.Base.extend(
        {
            $deleteLinks: null,
            $selectButton: null,
            // $spinner: null,

            init: function() {
                this.$deleteLinks = document.querySelectorAll('.deleteMappingLink');
                this.$selectButton = document.querySelector('#availableTickets-field .buttons .menubtn');
                // this.$spinner = $('<div class="spinner hidden" style="margin-left: 24px;" />').appendTo(this.$typeSelect);

                this.$deleteLinks.forEach(link => {
                    this.initDeleteButton(link);
                });
            },

            initDeleteButton(button) {
                this.addListener(button, 'click', (evt) => {
                    this.deleteTicketTypeMapping(evt);
                });
            },

            deleteTicketTypeMapping: function(evt) {
                const deleteBtn = evt.currentTarget;
                const ticketTypeHandle = deleteBtn.dataset['handle'];

                this.deleteAction(evt, ticketTypeHandle);
            },

            deleteAction(evt, ticketTypeHandle) {
                this.removeMappingBlock(ticketTypeHandle);
                this.showBlockTypeInMenu(ticketTypeHandle);

                this.showAddTicketTypeButton();
            },

            removeMappingBlock(typeHandle) {
                $(`#availableTickets-field .blocks #${typeHandle}`).remove();
            },

            showBlockTypeInMenu(ticketTypeHandle) {
                const blockTypeLink = document.querySelector(`.js-ticketTypeList li.${ticketTypeHandle}`)
                if (blockTypeLink) {
                    blockTypeLink.classList.remove('hidden');
                }
            },

            showAddTicketTypeButton() {
                $(this.$selectButton).removeClass('hidden');
            },
        });
})(jQuery);
