(function($) {
    /** global: Craft */
    /** global: Garnish */
    Craft.WaitlistToggle = Garnish.Base.extend(
        {
            $registrationToggle: null,
            $totalTickets: null,

            init: function() {
                this.$registrationToggle = $('#hasWaitingList');
                this.$totalTickets = $('#waitingListSize-field')[0];

                this.addListener(this.$registrationToggle, 'click', 'onChange');
            },

            onChange: function(evt) {
                const toggleIsOn = evt.currentTarget.getAttribute('aria-checked');

                if (toggleIsOn === 'true') {
                    this.$totalTickets.classList.remove('visually-hidden');
                } else {
                    this.$totalTickets.classList.add('visually-hidden');
                }
            }
        });
})(jQuery);
