<?php

function tortuga_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'tortuga_child_enqueue_styles');

function filter_frontpage_category($query) {
    if ($query->is_home() && $query->is_main_query()) {
        $query->set('cat', '19');  // Bruker kategori-ID 19
    }
}
add_action('pre_get_posts', 'filter_frontpage_category');

function add_epub_link_to_meta() {
    if (is_single() && function_exists('display_epub_link_theme')) {
        error_log("display_epub_link_theme exists");

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        // Define XML namespace for xlink
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?><root xmlns:xlink="http://www.w3.org/1999/xlink"></root>');

        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . ob_get_clean());

        $xpath = new DOMXPath($dom);
        $entry_meta = $xpath->query('//div[contains(@class, "entry-meta")]')->item(0);

        if ($entry_meta) {
            $link = display_epub_link_theme();

            $fragment = $dom->createDocumentFragment();
            // Add XML namespace for xlink
            $fragment->appendXML('<div xmlns:xlink="http://www.w3.org/1999/xlink">' . $link . '</div>');
            $entry_meta->appendChild($fragment);

            echo $dom->saveHTML();
            error_log('EPUB link inserted into entry-meta.');
        } else {
            error_log('Entry-meta div not found.');
        }
    } else {
        error_log('display_epub_link_theme function not found.');
    }
}
add_action('wp_footer', 'add_epub_link_to_meta', 20);

function add_epub_modal_html() {
    ?>
    <div id="epubModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="epubForm">
                <div class="form-group">
                    <label for="epubAuthor">Author:</label>
                    <input type="text" id="epubAuthor" name="author" class="form-control"><br>
                </div>
                <div class="form-group">
                    <label for="epubTitle">Title:</label>
                    <input type="text" id="epubTitle" name="title" class="form-control"><br>
                </div>
                <div class="form-group">
                    <label for="epubVersion">EPUB Version:</label>
                    <select id="epubVersion" name="version" class="form-control">
                        <option value="2">EPUB 2</option>
                        <option value="3" selected>EPUB 3</option>
                    </select><br>
                </div>
                <div class="form-group form-checkbox">
                    <label for="epubKepub">Convert to KEpub:</label>
                    <input type="checkbox" id="epubKepub" name="kepub">
                </div>
                <input type="hidden" id="epubPostId" name="post_id">
                <button type="submit" class="btn btn-primary">Convert</button>
            </form>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'add_epub_modal_html');

function enqueue_epub_converter_scripts() {
    wp_enqueue_script('wp-epub-converter', plugins_url('wp-epub-converter.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('wp-epub-converter', 'wpEpubConverter', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'default_author' => get_option('wp_epub_converter_author', ''),
    ));
    wp_enqueue_style('wp-epub-converter', plugins_url('wp-epub-converter.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_epub_converter_scripts');

function display_epub_link_theme() {
    if (is_single()) {
        $post_id = get_the_ID();
        $url = admin_url('admin-ajax.php') . '?action=generate_epub&post_id=' . $post_id;
        return '<span class="meta-epub">
            <svg class="icon icon-book" aria-hidden="true" role="img">
                <use xlink:href="' . get_template_directory_uri() . '/assets/icons/genericons-neue.svg#book"></use>
            </svg>
            <a href="#" class="button epub-link" data-post-id="' . $post_id . '">Download as EPUB</a>
        </span>';
    }
}
?>
