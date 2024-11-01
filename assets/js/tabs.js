jQuery(document).ready(function($) {
    $('.nav-tab-wrapper a').click(function(e) {
        e.preventDefault();
        var tab_id = $(this).attr('href');

        // Remove active class from all tabs
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');

        // Add active class to the clicked tab
        $(this).addClass('nav-tab-active');

        // Hide all tab content
        $('.tab-content').hide();

        // Show the tab content corresponding to the clicked tab link
        $(tab_id).show();
    });

    // Trigger click event on the first tab link to display its content initially
    $('.nav-tab-wrapper a:first').trigger('click');
});
