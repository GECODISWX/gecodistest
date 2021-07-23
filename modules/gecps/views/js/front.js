function resizeElementorCol(){
  $('.elementor-resize-col').each(function(i,e){
    var classes_txt = $(e).attr('class');
    var classes = classes_txt.split(' ');
    var to_find = '';
    var replace = '';
    for (var i = 0; i < classes.length; i++) {
      if (classes[i].includes('elementor-col-')) {
        to_find = classes[i];

      }
      if (classes[i].includes('elementor-resize-col-')) {
        replace = classes[i].replace('elementor-resize-col-','elementor-col-');
      }
    }
      $(e).attr('class',classes_txt.replace(to_find,replace));
  });
}

function makeSectionNav(){

  $('.home_cat_list').each(function(i,e){
    var model = $('.sec_nav_model').clone();
    var label = $(e).find('.block-content a').first().text().trim();
    var tmp = $(e).parents('.elementor-section').attr('class').split(' ');
    var color_class = tmp[tmp.length-2];
    var ico_class = tmp[tmp.length-1].replace('sec_','');
    console.log(color_class);
    model.find('.sec_nav_icon').addClass(ico_class+' fa cbp-mainlink-icon');
    model.find('.sec_nav_label').text(label);
    model.addClass('sec_nav sec_nav_'+color_class).removeClass('sec_nav_model');
    model.data('scroll-to','.'+color_class);
    model.appendTo('.home_section_nav');
  });
  $('.sec_nav').click(function(){
    var scroll_to = $(this).data('scroll-to');
    $("body,html").animate(
      {
        scrollTop: $(scroll_to).offset().top-100
      },
      200
    );
  })

  $(document).scroll(function() {

  var toBot = (window.innerHeight + window.scrollY + 300) >= document.body.scrollHeight;
  if (window.scrollY > 900 && !toBot) {
    $('.home_section_nav').fadeIn();
  } else {
    $('.home_section_nav').fadeOut();
  }
});

}

function openParentCategories(e){

}

function openBlckCatetories(){
  var current_cat_name = $('#category h1.h1.page-title').text();
  $('.block-categories .category-sub-link').each(function(i,e){
    if ($(e).text() == current_cat_name) {
      $(e).parent('li').addClass('current_cat');
      for (var i = 0; i < 3; i++) {
        var parent_li = $(e).parents("li[data-depth='"+i+"']");
        parent_li.addClass('current_cat').children('span.collapse-icons').click();
      }
    }
  });
}

function onPacksAddToCart(){
  $('.add_to_cart_pack').click(function(){
    var btns = $(this).parents('.elementor-products.row').find('button.btn.btn-product-list.add-to-cart').click();

  });
}

function unserialize(serializedData) {
    var urlParams = new URLSearchParams(serializedData); // get interface / iterator
    unserializedData = {}; // prepare result object
    for ([key, value] of urlParams) { // get pair > extract it to key/value
        unserializedData[key] = value;
    }

    return unserializedData;
}

function unpdateProductPage(e){
  $('.product-reference span').text(e.reference_to_display);
  $('.block-section .attachment').hide();
  $('.block-section .attachment_'+e.reference_to_display).show();

}

function hideOtherAttachments(){
  var current_ref = $('.product-reference>span').text();
  $('.block-section .attachment').hide();
  $('.block-section .attachment_'+current_ref).show();
}

function addExternalDataToForm(f){
  var form = $('.product-variants form');
  if(form.length>0){
    f +="&"+form.serialize();
  }
  return f;
}
function accessCookie(cookieName)
{
  var name = cookieName + "=";
  var allCookieArray = document.cookie.split(';');
  for(var i=0; i<allCookieArray.length; i++)
  {
    var temp = allCookieArray[i].trim();
    if (temp.indexOf(name)==0)
    return temp.substring(name.length,temp.length);
  }
  return "";
}
function createCookie(cookieName,cookieValue,daysToExpire)
{
  var date = new Date();
  date.setTime(date.getTime()+(daysToExpire*24*60*60*1000));
  document.cookie = cookieName + "=" + cookieValue + "; expires=" + date.toGMTString();
}

function devAlert(){
  var dev_host = ['habitat-et-jardin.fr','habitat-y-jardin.es'];
  var host = window.location.host;
  if (dev_host.includes(window.location.host)) {
    if (!accessCookie("in_dev")) {
      alert("Vous êtes sur le site DEV");
      createCookie("in_dev", "1", 1);
    }

  }

}

function makeCardAddressText(){
  if ($("#checkout-addresses-step").length>0) {
    var delivery_address_html = $("#delivery-addresses article.address-item.selected .address").html();

    if ($('#invoice-addresses').length>0) {
      var invoice_address_html = $("#invoice-addresses article.address-item.selected .address").html();
    }
    else {
      var invoice_address_html = delivery_address_html;
    }
    $(".card-block.card_address .delivery_address_text").append(delivery_address_html);
    $(".card-block.card_address .invoice_address_text").append(invoice_address_html);

  }
  if ($("#checkout-delivery-step").length>0) {
    var carrier_name = $(".delivery-options input:checked").parents(".row.delivery-option").find("span.h6.carrier-name").text();
    $(".card_carrier_text .carrier_title").append(carrier_name);
  }
}



$(document).ready(function(){

  if ($('body#index').length>0) {
    makeSectionNav();
  }

  if($('body#checkout').length>0){
    makeCardAddressText();
  }

  if ($('body#product').length>0) {
    hideOtherAttachments();
  }

  if ($('#category .block.block-toggle.block-categories.block-links.js-block-toggle').length>0) {
    openBlckCatetories();
  }

  if(typeof(sav_token) != 'undefined'){
    $('.sav_link').attr('href',sav_token);
  }

  if ($('#packs').length>0) {
    onPacksAddToCart();
  }
  devAlert();

});
