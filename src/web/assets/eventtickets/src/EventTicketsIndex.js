(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.EventTicketsIndex = Garnish.Base.extend(
        {
            $container: null,
            $main: null,
            isIndexBusy: false,

            $elements: null,
            $sourceLinks: null,
            page: 1,

            init: function($container) {
                this.initElements($container);
                this.initStatusLinks();
            },

            initElements($container) {
                this.$container = $container;
                this.$main = this.$container.find('.main');
                this.$elements = this.$container.find('.elements:first');
            },

            initStatusLinks() {
                this.$sourceLinks = this.$container.find('.sidebar:first a');

                Array.from(this.$sourceLinks).forEach((link) => {
                    link.addEventListener('click', (evt) => {
                        const { statusId, eventId } = evt.currentTarget.dataset;
                        this.getElementList(statusId, eventId);
                        this.updateActiveState(statusId);
                    })
                });
            },

            getElementList(statusId, eventId) {
                Craft.postActionRequest('eventsky/events/ticket-index-by-type', { 'statusId': statusId, 'eventId': eventId }, $.proxy(function(response, textStatus) {
                    if (textStatus === 'success') {
                        this.renderElementListing(response.html);
                    }
                }, this));
            },

            clearActiveState() {
                Array.from(this.$sourceLinks).forEach((link) => {
                    link.classList.remove('sel');
                });
            },

            setActiveState(statusId) {
                const activeLink = this.$container.find(`a[data-status-id="${statusId}"]`)[0];
                activeLink.classList.add('sel');
            },

            updateActiveState(statusId) {
                this.clearActiveState();
                this.setActiveState(statusId);
            },

            renderElementListing(html) {
                this.$elements[0].innerHTML = html;
            },
        });
})(jQuery);
