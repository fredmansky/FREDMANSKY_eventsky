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

            drag: null,
            drop: null,

            init: function($container) {
                this.initElements($container);
                this.initStatusLinks();
            },

            initElements($container) {
                this.$container = $container;
                this.$main = this.$container.find('.main');
                this.$elements = this.$container.find('.elements:first');
                this.$mainSpinner = this.$container.find('.spinner:first');
            },

            startLoading() {
                this.$mainSpinner[0].classList.remove('invisible');
                this.$main[0].classList.add('invisible');
            },

            stopLoading() {
                this.$mainSpinner[0].classList.add('invisible');
                this.$main[0].classList.remove('invisible');
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
                this.startLoading();
                Craft.postActionRequest('eventsky/events/ticket-index-by-type', { 'statusId': statusId, 'eventId': eventId }, $.proxy(function(response, textStatus) {
                    if (textStatus === 'success') {
                        this.renderElementListing(response.html);
                        this.stopLoading();
                    }
                }, this));
            },

            initDragAndDrop() {
                const dropTargets = [];

                console.log('');

                const onDropTargetChange = () => {
                    console.log('dropped item');
                };

                this.drag = new Garnish.DragDrop({
                    dropTargets,
                    onDropTargetChange,
                });

                this.drop = new Garnish.DragDrop({
                    dropTargets,
                    onDropTargetChange,
                });
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
