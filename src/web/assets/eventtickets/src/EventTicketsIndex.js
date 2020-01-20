(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.EventTicketsIndex = Garnish.Base.extend(
        {
            $container: null,
            $main: null,
            $mainSpinner: null,
            isIndexBusy: false,

            $elements: null,
            $sourceLinks: null,
            page: 1,

            init: function($container, settings) {
                // this.setSettings(settings, Craft.BaseElementIndex.defaults);
                this.initElements($container);
                this.initStatusLinks();
            },

            initElements($container) {
                this.$container = $container;
                this.$main = this.$container.find('.main');
                // this.$mainSpinner = this.$toolbarFlexContainer.find('.spinner:first');
                this.$elements = this.$container.find('.elements:first');
            },

            initStatusLinks() {
                this.$sourceLinks = this.$container.find('.sidebar:first a');

                Array.from(this.$sourceLinks).forEach((link) => {
                    link.addEventListener('click', (evt) => {
                        const { statusId, eventId } = evt.currentTarget.dataset;
                        this.getElementList(statusId, eventId);
                    })
                });
            },

            getElementList(statusId, eventId) {
                Craft.postActionRequest('eventsky/events/ticket-index-by-type', { 'statusId': statusId, 'eventId': eventId }, $.proxy(function(response, textStatus) {
                    // this.$spinner.addClass('hidden');
                    //
                    if (textStatus === 'success') {
                        this.renderElementListing(response.html);
                    }
                }, this));
            },

            renderElementListing(html) {
                this.$elements[0].innerHTML = html;
            },
        });
})(jQuery);
