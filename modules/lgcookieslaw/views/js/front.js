/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function closeinfo(accept, level) {
    closeBanners();

    if (typeof accept != 'undefined' && accept == true) {
        if (typeof level === 'undefined') {
            level = document.getElementById('lgcookieslaw-customization-enabled').checked == true ? 1 : 2;
        }

        setCookie(lgcookieslaw_cookie_name, level, lgcookieslaw_session_time);
    }

    if (typeof $.fancybox.open !== 'function') {
        $('#lgcookieslaw_modal').hide();
    }

    $.fancybox.close();

    if (lgcookieslaw_reload == true) {
        location.reload();
    }
}

function showBanner() {
    var banners = document.getElementsByClassName('lgcookieslaw-banner');

    if (banners) {
        for (var i = 0; i < banners.length; i++) {
            banners[i].style.display = 'table';
        }
    }

    if (lgcookieslaw_block) {
        $('.lgcookieslaw-overlay').css('display', 'block');
    }
}

function closeBanners() {
    var banners = document.getElementsByClassName('lgcookieslaw-banner');

    if (banners) {
        for (var i = 0; i < banners.length; i++) {
            banners[i].style.display = 'none';
        }
    }

    if (lgcookieslaw_block) {
        $('.lgcookieslaw-overlay').css('display', 'none');
    }
}

function checkLGCookie(cookie_name) {
    var regex = new RegExp("^(.*;)?\\s*" + cookie_name + "\\s*=\\s*[^;]+(.*)?$");

    return document.cookie.match(regex);
}

function deleteCookie(cname) {
    document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    document.cookie = cname + "=; domain=." + window.location.hostname + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    document.cookie = cname + "=; domain=." + window.location.hostname.replace('www.',  '') + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();

    d.setTime(d.getTime() + (exdays * 1000));

    var expires = "expires=" + d.toUTCString();

    document.cookie = cname + "=" + cvalue + ";" + expires + ";samesite=none;secure=true;path=/";
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function customizeCookies() {
    closeBanners();

    if (typeof $.fancybox.open === 'function') { 
        $.fancybox.open($("#lgcookieslaw_modal"), {
            autoSize : false,
            width:700,
            height:'auto',
            padding: 0,
            modal: true,
        });
    } else {
        $('#lgcookieslaw_modal').show();

        $('<a href="#lgcookieslaw_modal" />').fancybox({
            autoSize : false,
            width:700,
            height:'auto',
            padding: 0,
            modal: true,
        }).click();
    }
}

window.addEventListener('load',function() {
    if (checkLGCookie(lgcookieslaw_cookie_name)) {
        closeBanners();
    } else {
        showBanner();
    }

    $('#lgcookieslaw_cancel').click(function() {
        if (typeof $.fancybox.open !== 'function') {
            $('#lgcookieslaw_modal').hide();
        }

        $.fancybox.close();

        showBanner();
    });

    $('#lgcookieslaw-customization-enabled').change(function() {
        if ($(this).prop('checked')) {
            $('.lgcookieslaw-save').addClass('lggoogleanalytics-accept')
        } else {
            $('.lgcookieslaw-save').removeClass('lggoogleanalytics-accept')
        }
    });

    $('.lgcookieslaw-slider').click(function() {
        if ($(this).parent().find('input[type=checkbox]').is(':disabled')) {
            return false;
        }

        if ($(this).hasClass('lgcookieslaw-slider-checked')) {
            $(this).removeClass('lgcookieslaw-slider-checked');
        } else {
            $(this).addClass('lgcookieslaw-slider-checked');
        }
    });
});
