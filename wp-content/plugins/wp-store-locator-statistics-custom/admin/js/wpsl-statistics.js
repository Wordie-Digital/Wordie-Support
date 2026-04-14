 jQuery( document ).ready( function( $ ) {
	var map, markerSettings = {},
		latLngArray = [];

	/**
	 * Set the underscore template settings.
	 *
	 * Defining them here prevents other plugins
	 * that also use underscore / backbone, and defined a
	 * different _.templateSettings from breaking the
	 * rendering of the store locator template.
	 *
	 * @link	 http://underscorejs.org/#template
	 * @requires underscore.js
	 * @since	 1.0.0
	 */
	_.templateSettings = {
		evaluate: /\<\%(.+?)\%\>/g,
		interpolate: /\<\%=(.+?)\%\>/g,
		escape: /\<\%-(.+?)\%\>/g
	};

	if ( $( "#wpsl-stats-graph" ).length > 0 ) {

		// Load the Visualization API and the piechart / linechart package.
		google.charts.load('current', {'packages':['corechart', 'line']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback( showStats );
	}

	/**
	 * Show the different statistics sections.
	 *
	 * - Heatmap
	 * - Pie chart
	 * - Search graph
	 * - Nearby locations ( only on the address details page ).
	 *
	 * @since  1.0.0
	 * @return {void}
	 */
	function showStats() {
		
		// If there are no stats to export, then disable the CSV export button
		if ( !wpslStats.search_graph.total_search ) {
			$( ".wpsl-stats-export" ).addClass( "disabled" ).attr({ "title" : wpslStatsSettings.noExportData, "href" : "#" });

			$( ".wpsl-stats-export" ).on( "click", function() {
				return false;
			});
		}

		if ( $( "#wpsl-stats-wrap" ).hasClass( "wpsl-osm" ) ) {
			wpslStatsMap.service = "osm";
		} else {
			wpslStatsMap.service = "gmaps";
		}

		// Init the used map service ( Google Maps or OpenStreetMaps ( as of WPSL 3.0+ ) )
		wpslStatsMap[wpslStatsMap.service].init();

		// Create the search count graph
		drawSearchGraph( wpslStats.search_graph );

		// Create the piechart showing the term usage
		if ( typeof wpslStats.terms !== "undefined" && wpslStats.terms ) {
			drawPieChart( wpslStats.terms );
		} else {
			$( "#wpsl-stats-pie-chart" ).append( "<p class='wpsl-stats-no-piechart'>" + wpslStatsSettings.noDataFound + "</p>" );
		}

		if ( typeof wpslStats.services !== "undefined" && wpslStats.services ) {
			drawPieChartServices( wpslStats.services );
		} else {
			$( "#wpsl-stats-pie-chart-services" ).append( "<p class='wpsl-stats-no-piechart'>" + wpslStatsSettings.noDataFound + "</p>" );
		}

		// Create the heatmap showing the search locations on the map
		if ( typeof wpslStats.heatmap !== "undefined" ) {
			if ( wpslStats.heatmap.length ) {
				wpslStatsMap[wpslStatsMap.service].createHeatMap();
			} else {
				wpslStatsMap.utils.noHeatMapDataMsg();
			}
		}

		// Show the nearby locations on the map. Only happens on the search details page
		if ( typeof wpslStats.nearby !== "undefined" ) {
			wpslStatsMap.addNearbyLocations();
		}

		// Make sure the map data fits on the map
		wpslStatsMap[wpslStatsMap.service].fitBounds();
	}

	/**
	 * Handle the Google Maps / OpenStreetMaps
	 * and the required functionalities.
	 *
	 * @since   1.1.0
	 * @returns {void}
	 */
	wpslStatsMap = {
		service: '',
		startData: '',
		gmaps: {
			/**
			 * Init Google Maps.
			 *
			 * @since   1.0.0
			 * @returns {void}
			 */
			init: function() {
				var latLng, startLatLng, mapOptions;

				latLng		= wpslStatsSettings.startLatlng.split( "," );
				startLatLng = new google.maps.LatLng( latLng[0], latLng[1] );

				mapOptions = {
					center: startLatLng,
					mapTypeControl: false,
					streetViewControl: false,
					zoom: Number( wpslStatsSettings.zoomLevel )
				};

				// Get the correct marker path & properties
				markerSettings = wpslStatsMap.gmaps.getMarkerSettings();

				map	= new google.maps.Map( document.getElementById( "wpsl-stats-map" ), mapOptions );
			},
			/**
			 * Adjust the viewport so that all data is visible
			 *
			 * @since 1.1.0
			 * @returns {void}
			 */
			fitBounds: function() {
				var markerSrc,
					maxZoom = 12,
					bounds  = new google.maps.LatLngBounds();

				if ( typeof latLngArray[0] == "undefined" ) {
					return;
				}

				// Make sure we don't zoom to far
				google.maps.event.addListenerOnce( map, "bounds_changed", function( event ) {
					if ( this.getZoom() > maxZoom ) {
						this.setZoom( maxZoom );
					}

					if ( this.getZoom() == 0 ) {
						this.setZoom( 1 );
					}
				});

				/**
				 * Check if the latLng array is created by the heatmap function or the addMarker function.
				 *
				 * Both are structured slightly different, because the one from the addMarker
				 * also needs to hold the storeId which is required to make the markers
				 * bounce on the 'nearest locations' map.
				 */
				markerSrc = ( typeof latLngArray[0].storeId !== "undefined" ) ? true : false;

				$.each( latLngArray, function( index ) {
					if ( markerSrc ) {
						bounds.extend( latLngArray[index].position );
					} else {
						bounds.extend( latLngArray[index] );
					}
				});

				map.fitBounds( bounds );
			},
			/**
			 * Create the heatmap based on the search queries
			 *
			 * @since 1.1.0
			 * @returns {void}
			 */
			createHeatMap: function() {
				var heatmap, heatmapLatlng, respLatlng,
					heatmapData = [],
					gradient = [
						'rgba(0, 255, 255, 0)',
						'rgba(0, 255, 255, 1)',
						'rgba(0, 191, 255, 1)',
						'rgba(0, 127, 255, 1)',
						'rgba(0, 63, 255, 1)',
						'rgba(0, 0, 255, 1)',
						'rgba(0, 0, 223, 1)',
						'rgba(0, 0, 191, 1)',
						'rgba(0, 0, 159, 1)',
						'rgba(0, 0, 127, 1)',
						'rgba(63, 0, 91, 1)',
						'rgba(127, 0, 63, 1)',
						'rgba(191, 0, 31, 1)',
						'rgba(255, 0, 0, 1)'
					];

				$.each( wpslStats.heatmap, function( index ) {
					respLatlng	   = wpslStats.heatmap[index].split( "," );
					heatmapLatlng = new google.maps.LatLng( respLatlng[0], respLatlng[1] );
					heatmapData.push( heatmapLatlng );
				});

				heatmap = new google.maps.visualization.HeatmapLayer({
					data: new google.maps.MVCArray( heatmapData )
				});

				heatmap.setMap( map );
				heatmap.set( "gradient", gradient );

				/**
				 * Keep the heatmap data so we can later use it in the
				 * fitBounds() function to make sure the heatmap fits on the screen.
				 */
				latLngArray = heatmapData;
			},
			/**
			 * Make sure the coordinates are a google.map.LatLng instance.
			 *
			 * @since 1.1.0
			 * @param latLng
			 * @return {*} latLng
			 */
			checkLatLngInstance: function( latLng ) {
				if ( ! ( latLng instanceof google.maps.LatLng ) && typeof latLng.lat !== "undefined" && typeof latLng.lng !== "undefined" ) {
					latLng = new google.maps.LatLng( latLng.lat, latLng.lng );
				}

				return latLng;
			},
			/**
			 * Get the required marker settings.
			 *
			 * @since  1.0.0
			 * @return {object} settings The marker settings.
			 */
			getMarkerSettings: function() {
				var markerProp,
					markerProps = wpslStatsSettings.markerIconProps,
					settings	= {};

				for ( var key in markerProps ) {
					if ( markerProps.hasOwnProperty( key ) ) {
						markerProp = markerProps[key].split( "," );

						if ( markerProp.length == 2 ) {
							settings[key] = markerProp;
						}
					}
				}

				return settings;
			},
			marker: {
				/**
				 * Add the location markers to Google Maps.
				 *
				 * @since  1.0.0
				 * @param  {object} locationData The location data
				 * @param  {string} type		  The searched location, or the nearby store markers
				 * @return {void}
				 */
				add: function( locationData, type ) {
					var marker, mapIcon, latLng;

					//@todo later change the call to js file in WPSL 3.0+
					latLng = wpslStatsMap.gmaps.checkLatLngInstance( locationData );

					mapIcon = {
						url: wpslStatsMap.utils.getMarkerPath( type ),
						scaledSize: new google.maps.Size( Number( markerSettings.scaledSize[0] ), Number( markerSettings.scaledSize[1] ) ), //retina format
						origin: new google.maps.Point( Number( markerSettings.origin[0] ), Number( markerSettings.origin[1] ) ),
						anchor: new google.maps.Point( Number( markerSettings.anchor[0] ), Number( markerSettings.anchor[1] ) )
					};

					marker = new google.maps.Marker({
						position: latLng,
						map: map,
						title: wpslStatsMap.utils.decodeHtmlEntity( locationData.store ), //@todo later change the call to js file in WPSL 3.0+
						storeId: locationData.id,
						icon: mapIcon
					});

					/**
					 * Save the marker details so we can later use them to
					 * make sure all makers fit on the map with fitBounds().
					 */
					latLngArray.push( marker );
				},
				/**
				 * Add the blue ( start ) marker showing the searched location.
				 *
				 * @since   1.0.0
				 * @returns {void}
				 */
				addStartLocation: function() {
					wpslStatsMap.gmaps.marker.add( wpslStatsMap.startData );
				},
				/**
				 * Let a single marker bounce.
				 *
				 * @since	1.0.0
				 * @param	{number} storeId The storeId of the marker that we need to bounce on the map
				 * @param	{string} status  Indicates whether we should stop or start the bouncing
				 * @returns {void}
				 */
				bounce: function( storeId, status ) {
					var marker;

					// Find the correct marker to bounce based on the storeId.
					$.each( latLngArray, function( i ) {
						if ( latLngArray[i].storeId == storeId ) {
							marker = latLngArray[i];

							if ( status == "start" ) {
								marker.setAnimation( google.maps.Animation.BOUNCE );
							} else {
								marker.setAnimation( null );
							}
						}
					});
				}
			},
		},
		osm: {
			markerLayer: '',
			latLngBounds: '',
			/**
			 * Init OpenStreetMaps
			 *
			 * @since   1.1.0
			 * @returns {void}
			 */
			init: function() {
				var latLng, tileLayer;

				latLng = wpslStatsSettings.startLatlng.split( "," );

				this.markerLayer = new L.LayerGroup();
				tileLayer = L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">' + wpslStatsSettings.openStreetMap + '</a>',
					subdomains: ['a', 'b', 'c']
				});

				map = new L.Map( "wpsl-stats-map", {
					center: [ latLng[0], latLng[1] ],
					zoom: wpslStatsSettings.zoomLevel,
					layers: tileLayer
				});
			},
			/**
			 * Adjust the OpenStreetMap viewport so that all data is visible
			 *
			 * @since 1.1.0
			 * @returns {void}
			 */
			fitBounds: function() {
				var maxZoom = 12;

				// Convert the coordinates to a latLngBounds object
				if ( !_.isEmpty( latLngArray ) ) {
					this.latLngBounds = L.latLngBounds( latLngArray );
				}

				if ( typeof this.latLngBounds._northEast !== "object" ) {
					return;
				}

				map.once( "zoomend", function( e ) {
					if ( map.getZoom() > maxZoom ) {
						map.setZoom( maxZoom );
					}

					if ( map.getZoom() == 0 ) {
						map.setZoom( 1 );
					}
				});

				map.fitBounds( this.latLngBounds, { padding:[25, 25] } );
			},
			/**
			 * Create the heatmap based on the search queries
			 *
			 * @since 1.1.0
			 * @returns {void}
			 */
			createHeatMap: function() {
				var heatmapLayer, heatmapData,
					bounds = [],
					cfg = {
						'radius': 20,
						'maxOpacity': .8,
						'scaleRadius': false,
						'useLocalExtrema': true,
						latField: 'lat',
						lngField: 'lng',
						valueField: 'count'
					};

				heatmapLayer = new HeatmapOverlay( cfg );

				// Make sure the heatmap data consists of numbers, not strings.
				$.each( wpslStats.heatmap, function( index ) {
					var data = {};

					// Then loop over the object elements
					$.each( wpslStats.heatmap[index], function( key, value ) {
						data[key] = Number( value );
					});

					wpslStats.heatmap[index] = data;
				});

				heatmapData = {
					max: 8,
					data: wpslStats.heatmap
				};

				map.addLayer( heatmapLayer );

				heatmapLayer.setData( heatmapData );

				// Create the array that holds the coordinates for the fitBounds function.
				$.each( wpslStats.heatmap, function( index ) {
					$.each( wpslStats.heatmap[index], function( key, value ) {
						bounds.push( [wpslStats.heatmap[index]['lat'],wpslStats.heatmap[index]['lng']] );
					});
				});

				this.latLngBounds = L.latLngBounds( bounds );
			},
			/**
			 * Add the location markers to OpenStreetMaps.
			 *
			 * @since  1.1.0
			 * @param  {object} locationData The location data
			 * @param  {string} type		  The searched location, or the nearby store markers
			 * @return {void}
			 */
			marker: {
				markerObj: [],
				add: function ( locationData, type ) {
					var marker, mapIcon;

					mapIcon = L.icon({
						iconUrl: wpslStatsMap.utils.getMarkerPath( type ),
						iconSize: [24, 35],
						iconAnchor: [9, 21],
						popupAnchor: [3, -18]
					});

					marker = L.marker( [locationData.lat, locationData.lng], {
						clickable: false,
						icon: mapIcon,
						title: wpslStatsMap.utils.decodeHtmlEntity( locationData.store ), //@todo use the one from the wpsl-gmap.js after WPSL 3.0+
						alt: wpslStatsMap.utils.decodeHtmlEntity( locationData.store ),
						storeId: locationData.id
					});

					wpslStatsMap.osm.markerLayer.addLayer( marker );
					wpslStatsMap.osm.markerLayer.addTo( map );

					this.markerObj.push( marker );

					latLngArray.push( [ Number( locationData.lat ), Number( locationData.lng ) ] );
				},
				/**
				 * Add the blue ( start ) marker showing the searched location.
				 *
				 * @since   1.0.0
				 * @returns {void}
				 */
				addStartLocation: function() {
					wpslStatsMap.osm.marker.add( wpslStatsMap.startData );
				},
				/**
				 * Let a single marker bounce.
				 *
				 * @since	1.1.0
				 * @param	{number} storeId The storeId of the marker that we need to bounce on the map
				 * @param	{string} status  Indicates whether we should stop or start the bouncing
				 * @returns {void}
				 */
				bounce: function( storeId, status ) {
					var markers = this.markerObj;

					// Find the correct marker to bounce based on the storeId.
					$.each( markers, function( i ) {
						if ( markers[i].options.storeId == storeId ) {
							markers[i].bounce({
								duration: 1250,
								height: 50,
								loop: 1
							});
						}
					});
				}
			},
		},
		/**
		 * Add the 10 nearest locations to the map
		 *
		 * @returns {void}
		 */
		addNearbyLocations: function() {
			var nearbyLocations,
				storeData  = "",
				template   = $( "#wpsl-nearby-locations-template" ).html(),
				$storeList = $( ".wpsl-nearby-locations ul" );

			wpslStatsMap.startData = wpslStats.nearby.start;

			// It's only undefined is data for a non existing location is requested
			if ( typeof wpslStats.nearby.start == "undefined" ) {
				storeData = "<li>" + wpslStatsSettings.noDataFound + "</li>";
			} else {
				wpslStatsMap[wpslStatsMap.service].marker.addStartLocation();

				// Remove the old results.
				$storeList.empty();

				if ( typeof wpslStats.nearby.locations !== "undefined" && wpslStats.nearby.locations.length > 0 ) {
					nearbyLocations = wpslStats.nearby.locations;

					$.each( nearbyLocations, function( index ) {
						storeData = storeData + _.template( template )( nearbyLocations[index] );
						wpslStatsMap[wpslStatsMap.service].marker.add( nearbyLocations[index], 'nearby' );
					});
				} else {
					storeData = "<li>" + wpslStatsSettings.noNearbyLocations + "</li>";
				}
			}

			// Add the HTML for the nearby locations to the <ul>
			$storeList.append( storeData );

			// Make sure the markers bounce on mouseover.
			wpslStatsMap.utils.bindMarkerBounces();
		},
		utils: {
			/**
			 * Decode HTML entities.
			 *
			 * @link	https://gist.github.com/CatTail/4174511
			 * @since	1.1.0
			 * @param	{string} str The string to decode.
			 * @todo use the one from the wpsl-gmap.js after WPSL 3.0+
			 * @returns {string} The string with the decoded HTML entities.
			 */
			decodeHtmlEntity: function ( str ) {
				if ( str ) {
					return str.replace( /&#(\d+);/g, function( match, dec) {
						return String.fromCharCode( dec );
					});
				}
			},
			/**
			 * Unless it's changed with the 'wpsl_stats_js_settings' filter,
			 * the nearby marker is red, and the searched marker is blue.
			 *
			 * @since 1.1.0
			 * @param {string} type Make sure to get the correct marker image.
			 */
			getMarkerPath: function( type ) {
				var url;

				if ( type == 'nearby' ) {
					url = wpslStatsSettings.markerPath + wpslStatsSettings.nearbyMarker;
				} else {
					url = wpslStatsSettings.markerPath + wpslStatsSettings.searchedMarker;
				}

				return url;
			},
			/**
			 * Place a 'no data found' msg on top of the heatmap.
			 *
			 * @since	1.0.0
			 * @returns {void}
			 */
			noHeatMapDataMsg: function() {
				var msgWidth, msgOffset;

				// Add the 'no data found' to the map.
				$( "#wpsl-stats-map" ).append( "<div class='wpsl-stats-no-heatmap'>" + wpslStatsSettings.noDataFound + "</div>" );

				// Make sure it's positioned in the middle of the map.
				msgWidth  = $( ".wpsl-stats-no-heatmap" ).width();
				msgOffset = Math.floor( msgWidth / 2 );
				$( ".wpsl-stats-no-heatmap" ).width( msgWidth ).css( 'margin-left', -msgOffset + 'px' );
			},
			/**
			 * Make sure the markers bounce when the
			 * user hovers over the nearby location list.
			 *
			 * @since	1.1.0
			 * @returns {void}
			 */
			bindMarkerBounces: function() {
				$( ".wpsl-nearby-locations" ).on( "mouseenter", "li", function() {
					wpslStatsMap[wpslStatsMap.service].marker.bounce( $( this ).data( "store-id" ), "start" );
				});

				if ( wpslStatsMap.service == "gmaps" ) {
					$( ".wpsl-nearby-locations" ).on( "mouseleave", "li", function() {
						wpslStatsMap[wpslStatsMap.service].marker.bounce( $( this ).data( "store-id" ), "stop" );
					});
				}
			}
		},
	};

	/**
	 * Create the graph showing the
	 * amount of location searches.
	 *
	 * @since	1.0.0
	 * @param	{type} jsonData
	 * @returns {void}
	 */
	function drawSearchGraph( jsonData ) {
		var data, chart, options;

		data = new google.visualization.DataTable( jsonData.graph );
		options = {
			hAxis: {},
			vAxis: {
				ticks: jsonData.vticks
			},
			chartArea: {
				width: '90%',
				height: '85%'
			},
			legend: {
				position: 'none'
			},
			tooltip: {
				isHtml: true
			},
			pointSize: 5
		};

		// Adjust the hAxis options based on the used range ( hours / days ).
		if ( jsonData.range == 'hours' ) {
			options.hAxis = {
				gridlines: {
					count: -1,
					units: {
						hours: {
							format: ['HH:mm']
						}
					}
				}
			};
		} else {
			options.hAxis = {
				ticks: jsonData.hticks,
				format: 'MMM d',
			};
		}

		options.hAxis.gridlineColor = '#fff';

		// Remove the points from the graph if there are more then 30 results.
		if ( jsonData.graph.rows.length > 30 ) {
			options.pointSize = 0;
		}

		chart = new google.visualization.AreaChart( document.getElementById( "wpsl-stats-graph" ) );

		chart.draw( data, options );
	}

	/**
	 * Create the pie chart showing the used category filters.
	 *
	 * @since 1.0.0
	 * @param {string} pieData The JSON data required to create the pie chart.
	 */
	function drawPieChart( pieData ) {
		var data, chart, options;

		// Create our data table out of JSON data loaded from server.
		data = new google.visualization.DataTable( pieData );

		// Instantiate and draw our chart, passing in some options.
		chart = new google.visualization.PieChart( document.getElementById( "wpsl-stats-pie-chart" ) );

		options = {
			chartArea:{
				left: 20,
				top: 20,
				bottom: 20,
				width: '90%',
				height: '90%'
			},
			legend: {
				position: 'none'
			}
		};

		if ( $( "#wpsl-stats-wrap" ).hasClass( "wpsl-search-details" ) ) {
			options.legend.position = 'right';
		}

		chart.draw( data, options );
	}

	function drawPieChartServices( pieData ) {
		var data, chart, options;

		// Create our data table out of JSON data loaded from server.
		data = new google.visualization.DataTable( pieData );

		// Instantiate and draw our chart, passing in some options.
		chart = new google.visualization.PieChart( document.getElementById( "wpsl-stats-pie-chart-services" ) );

		options = {
			chartArea:{
				left: 20,
				top: 20,
				bottom: 20,
				width: '90%',
				height: '90%'
			},
			legend: {
				position: 'none'
			}
		};

		if ( $( "#wpsl-stats-wrap" ).hasClass( "wpsl-search-details" ) ) {
			options.legend.position = 'right';
		}

		chart.draw( data, options );
	}

	/*
     * Check the selected date range.
     *
     * If it's set to custom, then show the date fields
     * so the user can customize the range of the shown stats.
     */
	$( "#wpsl-change-stats-range" ).on( "change", function() {

		if ( $( this ).val() == 'custom' ) {
			$( ".wpsl-stats-custom-range" ).removeClass( "wpsl-hide" );

			enableDatePickers();
		} else {
			if ( $( ".wpsl-stats-custom-range" ).length > 0 ) {
				$( ".wpsl-stats-custom-range" ).addClass( "wpsl-hide" );
			}
		}
	});

	/*
     * Make sure the datepickers are working if
     * the loaded page shows a custom date range.
     */
	if ( $( "#wpsl-change-stats-range" ).val() == "custom" ) {
		enableDatePickers();
	}

	/**
	 * Enable the jQuery UI datepickers.
	 *
	 * @since	1.0.0
	 * @returns {void}
	 */
	function enableDatePickers() {
		var statsDates = $( "#wpsl-stats-start-date, #wpsl-stats-end-date" ).datepicker({
			dateFormat: "yy-mm-dd",
			maxDate: '+0D',
			onSelect: function( selectedDate ) {

				var option	 = this.id == "wpsl-stats-start-date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date	 = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );
				statsDates.not( this ).datepicker( "option", option, date );

				// If a value is set, then make sure to remove the required data class.
				if ( $( "#wpsl-stats-start-date" ).val() != '' ) {
					$( "#wpsl-stats-start-date" ).removeClass( "wpsl-required-date" );
					$( "#wpsl-stats-start-date" ).attr( 'value', $( "#wpsl-stats-start-date" ).val() );
				}

				if ( $( "#wpsl-stats-end-date" ).val() != '' ) {
					$( "#wpsl-stats-end-date" ).removeClass( "wpsl-required-date" );
				}
			}
		});

		checkCustomDates();
	}

	/**
	 * Make sure both custom filter dates exist.
	 *
	 * @since 1.0.0
	 * @returns boolean False if one of the date fields is left empty
	 */
	function checkCustomDates() {
		$( "#wpsl-load-stats" ).on( "click", function () {
			var emptyDate  = false,
				$startDate = $( "#wpsl-stats-start-date" ),
				$endDate   = $( "#wpsl-stats-end-date" );

			if ( typeof $startDate.val() !== "undefined" && $startDate.val().length == 0 ) {
				emptyDate = true;
				$startDate.addClass(" wpsl-required-date" );
			}

			if ( typeof $endDate.val() !== "undefined" && $endDate.val().length == 0 ) {
				emptyDate = true;
				$endDate.addClass( "wpsl-required-date" );
			}

			if ( emptyDate ) {
				return false;
			}
		});
	}
});
