/* -----------------------------------------------------------------------------
 *                 ___       __          _              _______
 *                /   | ____/ /___ ___  (_)___         / / ___/
 *               / /| |/ __  / __ `__ \/ / __ \   __  / /\__ \
 *              / ___ / /_/ / / / / / / / / / /  / /_/ /___/ /
 *             /_/  |_\__,_/_/ /_/ /_/_/_/ /_/   \____//____/
 *
 * ---------------------------------------------------------------------------*/
 (function( $ ) {
     'use strict';
 $.fn.onairmetabox = function( options ) {
        // Define single schedule timeslot
        function OnairForm( el ) {
            var form = {
                init : function( el ) {
                    this.$el = $( el );
                    this.cacheDom();
                    this.bindLibs();
                    return this;
                },
                // Cache all our dom elements
                cacheDom : function() {
                    this.showid  = $( '#post_ID' ).val();
                    this.$sday   = this.$el.find( '#sday' );
                    this.$stime  = this.$el.find( '#stime' );
                    this.$etime  = this.$el.find( '#etime' );
                    this.$submit = this.$el.find( '#mdmsm_submit' );
                    this.inputs  = [ this.$sday, this.$stime, this.$etime ];
                },
                // Bind external select2 library to our inputs
                bindLibs : function() {
                    $.each( this.inputs, function( index, el ) {
                        el.select2( {
                            width: '100%',
                        });
                    });
                },
                // Make sure all inputs have a value
                validate : function() {
                    return ( this.$sday.val() && this.$stime.val() && this.$etime.val() ) ? true : false;
                },
                // Enable submit button
                enable : function( event ) {
                    this.$submit.prop( 'disabled', !this.validate() );
                },
                // Clear all our form fields
                clear : function() {
                    $.each( this.inputs, function( index, el ) {
                        el.select2( 'val', '' );
                    });
                }
            };
            return form.init( el );
        }
        // Define View
        function OnairMetabox( el ) {
            var view = {
                init : function( el ) {
                    this.$el = el;
                    this.cacheDom();
                    this.emptyNotice();
                    return this;
                },
                // Cache all our dom elements
                cacheDom : function() {
                    this.$notice = this.$el.find( '#mdmsm_notice' );
                    this.$body   = this.$el.find( 'tbody' );
                },
                // Bind external select2 library to our inputs
                bindLibs : function() {
                    // Maybe use data table here, maybe not
                    return;
                },
                countRows : function() {
                    return this.$body.find( 'tr' ).length;
                },
                findIndex : function( $el ) {
                    var rowindex = -1;
                    $.each( this.$body.find( 'tr' ), function( index, element ) {
                        if( $( element ) == el ) {
                            console.log( '= Ok' );
                            rowindex = index;
                        }
                        if( $( element ).is( $el ) ) {
                            console.log( 'ID Ok' );
                            rowindex = index;
                        }
                    });
                    return rowindex;
                },
                // Add a row to the view
                add : function( json ) {
                    $( json.data ).hide().appendTo( this.$body ).fadeIn( 1000 );
                    var notice = $( '<div class="mdmsm-notice mdmsm-dismissible mdmsm-success"><p>Time Slot Successfully Saved</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
                    this.toggleNotices( notice );
                    return true;
                    // this.$body.toggle( 400 );
                },
                // Delete a row from the view
                remove : function( $el ) {
                    var self = this;
                    $el.fadeOut( 600 ).promise().done( function() {
                        $el.remove();
                        var notice = $( '<div class="mdmsm-notice mdmsm-dismissible mdmsm-success"><p>Time Slot Removed Successfully</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
                        self.toggleNotices( notice );
                        self.emptyNotice();
                    });
                    return true;
                },
                // Warn about Conflicts
                warn : function( json ) {
                    var notice = $( '<div class="mdmsm-notice mdmsm-dismissible mdmsm-warning"><p>' + json.error + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                    this.toggleNotices( notice );
                },
                // Toggle empty notices on and off
                toggleNotices : function( notice ) {
                    var notices = this.notices();
                    var self    = this;
                    // If not the result of an event, do this stuff
                    if( notice instanceof jQuery ){
                        if( notices.length ) {
                            var promises = [];
                            // Remove existing notices
                            $.each( notices, function( index, el ) {
                                var df = $.Deferred();
                                el.fadeOut( 400 ).remove().promise().done( function() {
                                    df.resolve();
                                });
                                promises.push( df );
                            });
                            // Add this notice
                            $.when.apply( $, promises ).then( function() {
                                notice.hide().appendTo( self.$notice).fadeIn( 400 );
                            });
                            return true;
                        } else {
                            // Just add the notice
                            self.$notice.hide().append( notice ).slideDown( 600 );
                            return true;
                        }
                    } else if( $( event.target ).hasClass( 'notice-dismiss' ) ) {
                        // Find parent
                        notice = $( event.target ).parents( '.mdmsm-notice' );
                        // Fade out and remove
                        notice.fadeOut( 600 ).promise().done( function(){
                            notice.remove();
                        });
                        return true;
                    }
                },
                notices : function() {
                    var notices = $.map( $('.mdmsm-notice'), function( el ) {
                        return $( el );
                    });
                    return notices;
                },
                emptyNotice : function() {
                    if( this.$body.find( 'tr' ).length === 0 ) {
                        var notice = $( '<div class="mdmsm-notice mdmsm-dismissible mdmsm-information"><p>This show has no time slots scheduled, why not schedule one now?</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
                        this.toggleNotices( notice );
                    }
                    return true;
                }
            };
            return view.init( el );
        }
        // Cache object
        var app = this;
        // Define Modules
        var build = function() {
            app.form   = new OnairForm( app.find( '#mdmsm_onair_form' ) );
            app.view   = new OnairMetabox( app.find( '#mdmsm_view' ) );
            app.showid = app.data( 'showid' );

        };
        // Bind dom events
        var bindEvents = function() {
            // Handler to enable submit button, bound to each input
            $.each( app.form.inputs, function( index, el ) {
                el.on( 'change', app.form.enable.bind( app.form ) );
            });
            // Handler for form submission
            app.form.$submit.on( 'click', submit.bind( app ) );
            app.view.$notice.on( 'click', '.notice-dismiss', app.view.toggleNotices.bind( app.view ) );
            app.view.$body.on( 'click', '.remove-record', remove.bind( app ) );
        };
        // Submit form
        var submit = function( event ) {
            event.preventDefault();
            app.form.$submit.attr( 'disabled', true );
            $.post( mdmsmajax.wpajaxurl, { action: 'add_metabox_row', showid : app.showid, sday : app.form.$sday.val(), stime : app.form.$stime.val(), etime : app.form.$etime.val(), }, function( response ) {
                // If successful, render
                if( response.success === true ) {
                    app.view.add( response );
                    app.form.clear();
                }
                // Else if error is specified, render warning
                else if( response.success === false && response.error ) {
                    app.view.warn( response );
                    app.form.$submit.attr( 'disabled', false );
                }
                else {
                    var json = {
                        error : response,
                    };
                    app.view.warn( json );
                    app.form.$submit.attr( 'disabled', false );
                }
            });
        };
        var remove = function( event ) {
            event.preventDefault();
            var $button = $( event.target );
            var $el     = $button.parents( 'tr' );
            var nonce   = $el.data( 'rid' );
            // Disable the button
            $button.attr( 'disabled', true );


            $.post( mdmsmajax.wpajaxurl, { action: 'remove_metabox_row', nonce: nonce, showid : app.showid }, function( response ) {
                // If successful, render
                if( response.success === true ) {
                    app.view.remove( $el );
                }
                // Else if error is specified, alert it
                else if( response.success === false && response.error ) {
                    app.view.warn( response );
                }
                // Else lets set a default message if we don't get a well formed response
                else {
                    var json = {
                        error : response,
                    };
                    app.view.warn( json );
                }
            });
        };
        this.init = function() {
            build();
            bindEvents();
            return this;
        };
        return this.init();
    }; // end plugin
}( jQuery ));

jQuery( document ).ready(function( $ ) {

    var metabox = $( '#mdmsm_onair_metabox' );

    if( metabox.length ) {
        var onair = metabox.onairmetabox();
    }

}); // end document ready
