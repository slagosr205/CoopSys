<div>
    <input type="hidden" id="latitud" name="latitud" value="{{ $get('latitud') }}">
    <input type="hidden" id="longitud" name="longitud" value="{{ $get('longitud') }}">
    
    <div id="map" style="height: 400px;"></div>
    
    <script>
        function initMap() {
            let lat = parseFloat(document.getElementById('latitud').value) || 14.6349;
            let lng = parseFloat(document.getElementById('longitud').value) || -90.5069;

            let map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: lat, lng: lng },
                zoom: 15
            });

            let marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                draggable: true
            });

            marker.addListener('dragend', function (event) {
                document.getElementById('latitud').value = event.latLng.lat();
                document.getElementById('longitud').value = event.latLng.lng();
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>
</div>
