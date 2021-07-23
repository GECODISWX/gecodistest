<div id="shop_selector" class="d-inline-block">
  <div class="shop-selector-wrapper d-inline-block">
      <div class="shop-selector dropdown js-dropdown">
          <a class="expand-more" data-toggle="dropdown">{l s='Shop :' mod='gecps'}  <img src="{$urls.img_lang_url}{$current_shop.iso}.jpg" alt="{l s='Our shop in'} : {$current_shop.name}"/> {$current_shop.name} <i class="fa fa-angle-down" aria-hidden="true"></i></a>
          <div class="dropdown-menu">
              <ul>
                  {foreach from=$shops item=shop}
                      <li {if $shop.id_shop == $current_shop.id_shop} class="current" {/if}>
                          <a href="{$shop.entity_url}" rel="alternate" class="dropdown-item"><img src="{$urls.img_lang_url}{$shop.iso}.jpg" alt="{l s='Our shop in'} : {$shop.country_name}"/> {$shop.country_name}</a>
                      </li>
                  {/foreach}
              </ul>
          </div>
      </div>
  </div>
</div>
