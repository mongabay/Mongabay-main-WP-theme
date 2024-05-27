(function ($) {

    $(window).load(function () {

    	jQuery(document).ready(function($) {
		    // Función para manejar el evento de clic en el botón "Load More"
		    $('.block-pagination-byline').on('click', function(e) {
		    	console.log('Click en el botón "Load More"');
		        e.preventDefault(); // Evita el comportamiento predeterminado del enlace

		        var button = $(this); // Captura el botón "Load More"
		        var data = {
		            'action': 'load_more_byline_terms', // Acción para el backend
		            'offset': $('.byline-grid-item').length, // Calcula el número de elementos ya cargados
		            // Puedes enviar otros datos que necesites para la consulta, por ejemplo: 'taxonomy': 'byline'
		        };

		        // Realiza una solicitud AJAX al backend
		        $.ajax({
		            url: tie.ajaxurl, // La URL del archivo admin-ajax.php es proporcionada por WordPress
		            type: 'POST',
		            data: data,
		            beforeSend: function() {
		                // Muestra algún tipo de indicador de carga, si lo deseas
		                button.text('Loading...'); // Cambia el texto del botón mientras se carga
		            },
		            success: function(response) {
		                // Inserta los nuevos términos en el grid
		                $('.byline-grid').append(response);
		                button.text('Load More'); // Restaura el texto del botón
		            }
		        });
		    });
		});

    });
	
})(jQuery);