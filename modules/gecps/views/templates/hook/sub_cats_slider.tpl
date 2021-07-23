<div id="sub_cats_slider_section" >
  <div class="elementor-section elementor-element elementor-element-rns8vpd elementor-top-section elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-element_type="section">
     <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-row">
           <div class="elementor-column elementor-element elementor-element-16swag5 elementor-col-100 elementor-top-column" data-element_type="column">
              <div class="elementor-column-wrap">
                 <div class="elementor-widget-wrap">
                    <div class="elementor-widget elementor-element elementor-element-ewp4qxf elementor-widget-image-carousel" data-element_type="image-carousel">
                       <div class="elementor-widget-container">
                          <div class="elementor-image-carousel-wrapper elementor-slick-slider" >
                             <div class="elementor-image-carousel slick-arrows-inside" data-slider_options='{ "slidesToShow":9,"slidesToShowTablet":3,"slidesToShowMobile":3,"autoplaySpeed":5000,"autoplay":false,"infinite":false,"pauseOnHover":false,"speed":500,"arrows":true,"dots":false,"slidesToScroll":4 }'>
                               {foreach from=$sub_cats item=sub_cat key=key}
                               <div>
                                  <div class="slick-slide-inner sub_cat">

                                    {if $sub_cat.0.image}
                                      <a href="{$sub_cat.0.url}">
                                        <div class="sub_img_holder">
                                          <img src="{$sub_cat.0.image.bySize.sub_cat.url}" alt="{$sub_cat.0.name}" width="{$sub_cat.0.image.bySize.sub_cat.width}"
                                           height="{$sub_cat.0.image.bySize.sub_cat.height}" class="img-fluid slick-slide-image"/>
                                        </div>

                                      </a>
                                    {else}
                                      <div class="sub_img_holder">
                                        <img class="slick-slide-image" src="https://habitat-et-jardin.fr/modules/iqitelementor/views/images/placeholder.png" alt="Image alt" />
                                      </div>


                                    {/if}
                                    <ul class="sub_cat_list">

                                      {foreach from=$sub_cat item=sub_sub_cat key=key}
                                        {if $key < 10}
                                          <li class="{if $key >3}more{/if}"> <a class="{if $key==0}sub_cat_name{else}sub_sub_cat_name{/if}" href="{$sub_sub_cat.url}">{$sub_sub_cat.name}</a></li>
                                        {/if}
                                      {/foreach}
                                      {if $sub_cat|count >=10}
                                        <li class="more more_cats"> <a class="sub_sub_cat_name" href="{$sub_cat.0.url}">{l s="see all categories" mod="gecps"} >></a></li>
                                      {/if}
                                    </ul>
                                    {if $sub_cat|count >4}
                                      <a class="sub_cat_more" href="#"><i class="fas fa-chevron-down"></i></a>
                                    {/if}


                                  </div>
                               </div>
                               {/foreach}
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
