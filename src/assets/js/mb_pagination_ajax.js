(function ($) {
    var MBEI_hooks = {};
    var canBeLoaded = true;

    function MBEI_add_action(name, func) {
        if (!MBEI_hooks[ name ])
            MBEI_hooks[ name ] = [];
        MBEI_hooks[ name ].push(func);
    }

    function MBEI_do_action(name, ...params) {
        if (MBEI_hooks[ name ])
            MBEI_hooks[ name ].forEach(func => func(...params));
    }

    function mbei_load_more() {
        widget = $(this);
        settings = widget.attr("data-settings");
        args = JSON.parse(settings);
        widget.children(".elementor-button").attr("data-widget_id", args.widget_id);
        widget.children(".elementor-button").on('click', function(e){
            e.preventDefault();
            mbei_load_next_page($(this).data('widget_id'));
        })
    }

    function mbei_load_next_page(id) {

        widget = $(".elementor-element[data-id='" + id + "'] .mbei-posts");
        settings = widget.attr("data-settings");
        args = JSON.parse(settings);

        posts = $(".elementor-element[data-id='" + args.widget_id + "'] .mbei-posts");

        if (args.load_method == 'loadmore') {
            button_text = $(".elementor-element[data-id='" + args.widget_id + "'] .mbei-load-more-button .elementor-button"); // add this .elementor-element[data-id='" + args.widget_id + "']
            button = $(".elementor-element[data-id='" + args.widget_id + "'] .mbei-load-more-button");
            attb = JSON.parse(button.attr("data-settings"));
        }
        if (args.load_method == 'lazyload') {
            animation = $(".elementor-element[data-id='" + args.widget_id + "'] .mbei-lazyload");
        }
        data = {
            'action': 'mbeiload',
            'query': mbei_ajax_params.posts,
            'mbei_ajax_settings': settings,
            'mbei_ajax_nonce': mbei_ajax_params.mbei_ajax_nonce
        };

        $.ajax({
            url: mbei_ajax_params.ajaxurl, // AJAX handler
            data: data,
            type: 'POST',
            beforeSend: function (xhr) {
                if (args.load_method == 'loadmore')
                    button_text.html(attb.loading_text); // change the button text, you can also add a preloader image
                canBeLoaded = false;
            },
            success: function (data) {
                if (data) {
                    posts.append(data); // insert new posts
                    args.current_page++;
                    if (args.load_method == 'loadmore') {
                        button_text.html(attb.text);
                        button_text.blur( );
                    }
                    newsettings = JSON.stringify(args);
                    widget.attr("data-settings", newsettings);


                    if (args.load_method == 'lazyload') {
                        $(animation).addClass("animation-hidden");
                    }
                    //here you need to take care of linkable items and masonry !!!!!!!!!!!!!!!!!
                    MBEI_do_action('ajax', args);

                    if (args.current_page == args.max_num_pages) {
                        if (args.load_method == 'loadmore')
                            button.remove( ); // if last page, remove the button
                        if (args.load_method == 'lazyload')
                            animation.remove( );
                    }
                    canBeLoaded = true;

                    if (typeof ECScheckInView !== 'undefined')
                        ECScheckInView( );
                    // you can also fire the "post-load" event here if you use a plugin that requires it
                    // $( document.body ).trigger( 'post-load' );
                } else {
                    if (args.load_method == 'loadmore') {
                        button.remove( );
                    } // if no data, remove the button as well
                    if (args.load_method == 'lazyload') {
                        animation.remove( );
                    }
                }
            }
        });
    }

    function MBEI_SkinChangeUrlPage(args) {
        if (!args.change_url)
            return;
        regex = /\/page\/[0-9]+\//gm;
        currenturl = window.location.pathname;
        newurl = currenturl.replace(regex, '/');
        newurl = newurl + 'page/' + args.current_page + '/';
        history.pushState({urlPath: newurl}, "", newurl);
    }

    function MBEI_SkinReInitJs(args) {
        if (!args.reinit_js)
            return;
        $('.elementor-element-' + args.widget_id + ' .elementor-element').each(function ( ) {
            elementorFrontend.elementsHandler.runReadyTrigger($(this));
        });
        console.log(args.reinit_js);
    }

    $(document).ready(function ( ) {
        $('.mbei-lazyload').addClass("animation-hidden");
        $('.mbei-lazyload a').css("display", "none");

        $('.mbei-load-more-button').each(mbei_load_more);

        MBEI_add_action("ajax", function (args) {
            MBEI_SkinChangeUrlPage(args)
        });
        MBEI_add_action("ajax", function (args) {
            MBEI_SkinReInitJs(args)
        });
    });
})(jQuery);