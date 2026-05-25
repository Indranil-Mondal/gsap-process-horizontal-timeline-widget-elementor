( function () {
    'use strict';

    // Step positions as a fraction (0–1) along the timeline
    // Evenly spaced for up to 10 steps; extras default to 1.0
    function getStepPositions( count ) {
        var positions = [];
        for ( var i = 0; i < count; i++ ) {
            positions.push( count === 1 ? 0 : i / ( count - 1 ) );
        }
        return positions;
    }

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function initDesktop( steps, positions ) {
        var lineFill = document.getElementById( 'lineFill' );
        var lineGlow = document.getElementById( 'lineGlow' );
        if ( ! lineFill ) return;

        var tl = gsap.timeline( {
            scrollTrigger: {
                trigger: '.timeline-wrapper',
                start: 'top 65%',
                end:   'bottom 40%',
                scrub: 2,
            }
        } );

        tl.to( lineFill, { scaleX: 1, ease: 'none', duration: 1 }, 0 );
        tl.to( lineGlow, { opacity: 1, duration: 0.001 }, 0.001 );
        tl.to( lineGlow, { opacity: 0, duration: 0.001 }, 0.999 );

        steps.forEach( function ( step, i ) {
            tl.to( step, {
                opacity: 1,
                y: 0,
                duration: 0.12,
                ease: 'power2.out',
                onStart: function () { step.classList.add( 'is-active' ); },
                onReverseComplete: function () { step.classList.remove( 'is-active' ); }
            }, positions[ i ] );
        } );
    }

    function initMobile( steps, positions ) {
        var verticalFill = document.getElementById( 'verticalFill' );
        if ( ! verticalFill ) return;

        var tl = gsap.timeline( {
            scrollTrigger: {
                trigger: '.timeline-wrapper',
                start: 'top 70%',
                end:   'bottom 35%',
                scrub: 1.4,
            }
        } );

        tl.to( verticalFill, { height: '100%', ease: 'none', duration: 1 }, 0 );

        steps.forEach( function ( step, i ) {
            tl.to( step, {
                opacity: 1,
                y: 0,
                duration: 0.12,
                ease: 'power2.out',
                onStart: function () { step.classList.add( 'is-active' ); },
                onReverseComplete: function () { step.classList.remove( 'is-active' ); }
            }, positions[ i ] );
        } );
    }

    var currentMode = null;

    function init() {
        var mobile = isMobile();
        if ( mobile === currentMode ) return;
        currentMode = mobile;

        ScrollTrigger.getAll().forEach( function ( st ) { st.kill(); } );
        gsap.killTweensOf( '.step, #lineFill, #lineGlow, #verticalFill' );

        var steps     = document.querySelectorAll( '.step' );
        var positions = getStepPositions( steps.length );

        gsap.set( steps,           { opacity: 0, y: 16 } );
        gsap.set( '#lineFill',     { scaleX: 0 } );
        gsap.set( '#lineGlow',     { opacity: 0 } );
        gsap.set( '#verticalFill', { height: '0%' } );

        if ( mobile ) {
            initMobile( steps, positions );
        } else {
            initDesktop( steps, positions );
        }

        ScrollTrigger.refresh();
    }

    // Run after DOM is ready
    document.addEventListener( 'DOMContentLoaded', function () {
        if ( typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined' ) return;
        gsap.registerPlugin( ScrollTrigger );
        init();
    } );

    // Debounced resize
    var resizeTimer;
    window.addEventListener( 'resize', function () {
        clearTimeout( resizeTimer );
        resizeTimer = setTimeout( init, 200 );
    } );

} )();
