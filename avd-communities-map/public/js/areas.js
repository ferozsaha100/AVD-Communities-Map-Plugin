/**
 * AVD Communities Map — Areas Embed JS
 * Supports Google Maps and Mapbox GL JS.
 * Multi-instance: each .avdc-areas section is independent.
 * Config stored in window.avdcAreasInstances[instanceId] (Elementor)
 * or window.avdcAreasConfig (shortcode legacy).
 */
(function () {
  'use strict';

  // Per-instance init functions, keyed by data-avdc-id value.
  var _instances = {};

  // ══════════════════════════════════════════════════════════════════════
  //  PER-SECTION FACTORY
  // ══════════════════════════════════════════════════════════════════════
  function createInstance(section) {
    var iid = (section.dataset.avdcId !== undefined) ? section.dataset.avdcId : '';

    // Config resolution: Elementor widgets use avdcAreasInstances[id],
    // shortcode (iid='') falls back to avdcAreasConfig.
    var cfg;
    if (iid !== '' && window.avdcAreasInstances && window.avdcAreasInstances[iid]) {
      cfg = window.avdcAreasInstances[iid];
    } else {
      cfg = window.avdcAreasConfig || {};
    }

    var AREAS    = cfg.areas        || {};
    var PROVIDER = cfg.map_provider || 'google';

    var C_BG     = cfg.map_bg      || '#0e0e2e';
    var C_ROAD   = cfg.map_road    || '#1a1a50';
    var C_HWY    = cfg.map_highway || '#302e60';
    var C_WATER  = cfg.map_water   || '#060620';
    var C_LABEL  = cfg.map_label   || '#8a8ab0';
    var C_BORDER = cfg.map_border  || '#302eb7';
    var C_MARKER = cfg.map_marker  || '#B87F0D';

    // Scoped element IDs — empty iid keeps legacy shortcode IDs working.
    var mapId   = iid ? 'avdc-map-'      + iid : 'avdc-map';
    var cityId  = iid ? 'avdcMapCity-'   + iid : 'avdcMapCity';
    var phId    = iid ? 'avdcPlaceholder-' + iid : 'avdcPlaceholder';
    var phCityId = iid ? 'avdcPhCity-'   + iid : 'avdcPhCity';

    var avdcMap    = null;
    var avdcMarker = null;

    function firstArea() {
      var keys = Object.keys(AREAS);
      return keys.length ? AREAS[ keys[0] ] : { lat: 42.3223, lng: -83.1763, zoom: 13 };
    }

    // ── GOOGLE MAPS ────────────────────────────────────────────────────
    function initMap() {
      if ( PROVIDER !== 'google' ) return;
      var el = document.getElementById( mapId );
      var ph = document.getElementById( phId );
      if ( !el ) return;
      if ( ph ) ph.remove();

      var first = firstArea();

      avdcMap = new google.maps.Map( el, {
        center:           { lat: first.lat, lng: first.lng },
        zoom:             first.zoom,
        disableDefaultUI: true,
        zoomControl:      true,
        styles: [
          { elementType: 'geometry',                                     stylers: [{ color: C_BG }] },
          { elementType: 'labels.text.fill',                             stylers: [{ color: C_LABEL }] },
          { elementType: 'labels.text.stroke',                           stylers: [{ color: C_BG }] },
          { featureType: 'road',         elementType: 'geometry',        stylers: [{ color: C_ROAD }] },
          { featureType: 'road',         elementType: 'geometry.stroke', stylers: [{ color: C_BG }] },
          { featureType: 'road.highway', elementType: 'geometry',        stylers: [{ color: C_HWY }] },
          { featureType: 'water',        elementType: 'geometry',        stylers: [{ color: C_WATER }] },
          { featureType: 'water',        elementType: 'labels.text.fill',stylers: [{ color: C_LABEL }] },
          { featureType: 'poi',          stylers: [{ visibility: 'off' }] },
          { featureType: 'administrative', elementType: 'geometry',
            stylers: [{ color: C_BORDER }, { weight: 1.5 }] },
        ],
      });

      avdcMarker = new google.maps.Marker({
        position:  { lat: first.lat, lng: first.lng },
        map:       avdcMap,
        icon: {
          path:         google.maps.SymbolPath.CIRCLE,
          fillColor:    C_MARKER,
          fillOpacity:  1,
          strokeColor:  C_MARKER,
          strokeWeight: 2,
          scale:        10,
        },
        animation: google.maps.Animation.DROP,
      });
    }

    // ── MAPBOX GL JS ───────────────────────────────────────────────────
    function initMapbox() {
      if ( PROVIDER !== 'mapbox' ) return;
      if ( typeof mapboxgl === 'undefined' ) return;

      var el = document.getElementById( mapId );
      var ph = document.getElementById( phId );
      if ( !el ) return;
      if ( ph ) ph.remove();

      mapboxgl.accessToken = cfg.mapbox_key || '';

      var first = firstArea();

      avdcMap = new mapboxgl.Map({
        container:          mapId,
        style:              cfg.mapbox_style || 'mapbox://styles/mapbox/dark-v11',
        center:             [ first.lng, first.lat ],
        zoom:               first.zoom,
        attributionControl: false,
      });

      avdcMap.addControl( new mapboxgl.NavigationControl({ showCompass: false }), 'bottom-right' );

      var markerEl       = document.createElement('div');
      markerEl.className = 'avdc-mapbox-marker';
      markerEl.style.cssText =
        'width:18px;height:18px;border-radius:50%;'
        + 'background:' + C_MARKER + ';'
        + 'border:2.5px solid #fff;'
        + 'box-shadow:0 0 0 3px ' + C_MARKER + '66,0 2px 8px rgba(0,0,0,.4);'
        + 'transition:transform .3s;cursor:pointer;';

      avdcMarker = new mapboxgl.Marker({ element: markerEl, anchor: 'center' })
        .setLngLat([ first.lng, first.lat ])
        .addTo( avdcMap );
    }

    // ── PAN / FLY TO ────────────────────────────────────────────────────
    function avdcPan( cityName ) {
      var d = AREAS[ cityName ];
      if ( !d ) return;

      var cityEl = document.getElementById( cityId );
      if ( cityEl ) {
        cityEl.classList.remove('avdc-vis');
        setTimeout(function () {
          cityEl.textContent = cityName + ', ' + d.state;
          cityEl.classList.add('avdc-vis');
        }, 160);
      }

      var phCity = document.getElementById( phCityId );
      if ( phCity ) phCity.textContent = cityName + ', ' + d.state;

      if ( !avdcMap || !avdcMarker ) return;

      if ( PROVIDER === 'mapbox' ) {
        avdcMap.flyTo({ center: [ d.lng, d.lat ], zoom: d.zoom, duration: 800, essential: true });
        avdcMarker.setLngLat([ d.lng, d.lat ]);
        var el = avdcMarker.getElement();
        if ( el ) {
          el.style.transform = 'scale(1.5)';
          setTimeout(function () { el.style.transform = 'scale(1)'; }, 400);
        }
      } else {
        avdcMap.panTo({ lat: d.lat, lng: d.lng });
        avdcMap.setZoom( d.zoom );
        avdcMarker.setPosition({ lat: d.lat, lng: d.lng });
        avdcMarker.setAnimation( google.maps.Animation.BOUNCE );
        setTimeout(function () { avdcMarker.setAnimation(null); }, 720);
      }
    }

    // ── CARD EVENT LISTENERS (scoped to this section) ──────────────────
    section.querySelectorAll('.avdc-areas__card').forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        section.querySelectorAll('.avdc-areas__card').forEach(function (c) {
          c.classList.remove('avdc-active');
        });
        card.classList.add('avdc-active');
        avdcPan( card.dataset.city );
      });
      card.addEventListener('click', function () {
        var link = card.dataset.link;
        if ( link ) window.location.href = link;
      });
    });

    var cityEl = document.getElementById( cityId );
    if ( cityEl ) cityEl.classList.add('avdc-vis');

    // Boot map provider
    if ( PROVIDER === 'mapbox' ) {
      if ( typeof mapboxgl !== 'undefined' ) {
        initMapbox();
      } else {
        var attempts = 0;
        var poll = setInterval(function () {
          attempts++;
          if ( typeof mapboxgl !== 'undefined' ) {
            clearInterval( poll );
            initMapbox();
          } else if ( attempts >= 50 ) {
            clearInterval( poll );
          }
        }, 100 );
      }
    } else {
      // Google: will be called by the global avdcInitMap dispatcher
      if ( window.google && window.google.maps ) {
        initMap();
      }
    }

    return { initMap: initMap };
  }

  // ══════════════════════════════════════════════════════════════════════
  //  GLOBAL GOOGLE MAPS CALLBACK
  //  Called by avdGoogleMapsReady (set by shortcode/widget inline script).
  //  Iterates all registered instances and fires each section's initMap.
  // ══════════════════════════════════════════════════════════════════════
  window.avdcInitMap = function () {
    Object.keys( _instances ).forEach(function ( key ) {
      if ( _instances[ key ] && typeof _instances[ key ].initMap === 'function' ) {
        _instances[ key ].initMap();
      }
    });
  };

  // ══════════════════════════════════════════════════════════════════════
  //  DOM READY — bootstrap every .avdc-areas section on the page
  // ══════════════════════════════════════════════════════════════════════
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.avdc-areas').forEach(function (section) {
      var key = section.dataset.avdcId !== undefined ? section.dataset.avdcId : '__sc__';
      _instances[ key ] = createInstance( section );
    });

    // Handle Google Maps already loaded before DOMContentLoaded (e.g. cached)
    if ( window.google && window.google.maps ) {
      window.avdcInitMap();
    }
  });

})();
