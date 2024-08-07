jQuery(document).ready(function($) {
    // Event listener for EPUB-knappen
    $(document).on('click', '.epub-link', function(event) {
        event.preventDefault(); // Forhindre standard link-oppførsel

        var postId = $(this).data('post-id');
        console.log('EPUB button clicked. Post ID: ' + postId);

        // Sett inn post-ID i skjult felt i modal-vinduet
        $('#epubPostId').val(postId);

        // Hent postens tittel fra siden og sett den inn i Title-feltet
        var postTitle = $('.entry-title').text();
        $('#epubTitle').val(postTitle);

        // Hent forfatterens navn fra meta-informasjonen (eller bruk en standardverdi)
        var postAuthor = $('.meta-author .author.vcard .fn').text() || wpEpubConverter.default_author;
        $('#epubAuthor').val(postAuthor);

        // Vis modal-vinduet
        $('#epubModal').show();
    });

    // Event listener for å lukke modal-vinduet når X-knappen (close) klikkes
    $(document).on('click', '.modal .close', function() {
        $('#epubModal').hide();
    });

    // Event listener for å lukke modal-vinduet når brukeren klikker utenfor modal-vinduet
    $(window).on('click', function(event) {
        if ($(event.target).is('#epubModal')) {
            $('#epubModal').hide();
        }
    });
});
