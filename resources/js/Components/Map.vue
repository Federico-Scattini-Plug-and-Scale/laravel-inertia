<template>
	<div id="map"></div>
</template>

<script>
import 'leaflet/dist/leaflet.js';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.markercluster/dist/leaflet.markercluster.js';

export default {
  	data() {
		return {
		}
	},
	props: {
		markers: Object
	},
	mounted() {
		let map = L.map('map').setView([51.505, -0.09], 13);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Points &copy 2012 LINZ',
			maxZoom: 18,
			tileSize: 512,
			zoomOffset: -1,
		}).addTo(map);
		
		let markers = L.markerClusterGroup({ chunkedLoading: true });
		
		this.markers.forEach(item => {
			let marker = L.marker(L.latLng(item.latitude, item.longitude), { title: item.title })
			marker.bindPopup(item.title);
			markers.addLayer(marker)
		});

		map.addLayer(markers);
	}
}
</script>