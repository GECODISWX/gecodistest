{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

<script type="text/javascript">
    function addIP()
    {ldelim}
        document.getElementById('PS_LGCOOKIES_IPTESTMODE').value='{$smarty.server.REMOTE_ADDR|escape:'javascript':'UTF-8'}';
    {rdelim}


    $(document).ready(function(){ldelim}
        $('div#content').addClass('bootstrap').removeClass('nobootstrap');
        if (typeof helper_tabs != 'undefined' && typeof unique_field_id != 'undefined')
        {ldelim}
            $.each(helper_tabs, function(index) {ldelim}
                $('#'+unique_field_id+'fieldset_'+index+' .form-wrapper').prepend('<div class="tab-content panel" />');
                $('#'+unique_field_id+'fieldset_'+index+' .form-wrapper').prepend('<ul class="nav nav-tabs" />');
                $.each(helper_tabs[index], function(key, value) {ldelim}
                    // Move every form-group into the correct .tab-content > .tab-pane
                    $('#'+unique_field_id+'fieldset_'+index+' .tab-content').append('<div id="'+key+'" class="tab-pane" />');
                    var elemts = $('#'+unique_field_id+'fieldset_'+index).find("[data-tab-id='" + key + "']");
                    $(elemts).appendTo('#'+key);
                    // Add the item to the .nav-tabs
                    if (elemts.length != 0)
                        $('#'+unique_field_id+'fieldset_'+index+' .nav-tabs').append('<li><a href="#'+key+'" data-toggle="tab">'+value+'</a></li>');
                    {rdelim});
                // Activate the first tab
                $('#'+unique_field_id+'fieldset_'+index+' .tab-content div').first().addClass('active');
                $('#'+unique_field_id+'fieldset_'+index+' .nav-tabs li').first().addClass('active');
                {rdelim});
            {rdelim}
        $('.nav-tabs a').click(function(){ldelim}

            inputs = $('.form-wrapper div.form-group:not(.translatable-field)');
            var active = $(this).attr('href').replace('#', '');
            $.each(inputs, function(index, item){ldelim}
                if ($(item).attr('data-tab-id') == active)
                {
                    $(item).show();
                }
                else
                {
                    $(item).hide();
                }
                {rdelim});

            {rdelim});
        $('.nav-tabs li:first-child a').trigger('click');
        $.each(languages, function (key, item){
           if (item['is_default'] == 1)
               $('.translatable-field.lang-'+item['id_lang']).show();

        });
        {rdelim});
</script>


