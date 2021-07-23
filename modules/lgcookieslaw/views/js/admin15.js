/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */
 
$(document).ready(function(){
		$('#content').addClass('bootstrap');
		$('.nav li a').click(function(){
			 $('.nav li').removeClass('active');
			 $('.tab-pane').removeClass('active');
			 $($(this).attr('href')).addClass('active');
			 $(this).closest('li').addClass('active');
			 return false;
		});	
		$('.lang-btn').click(function(){
			$(this).closest('div').addClass('open')
		});
});
function hideOtherLanguage(id)
{
	$('.translatable-field').hide();
	$('.lang-' + id).show();

	var id_old_language = id_language;
	id_language = id;

}
