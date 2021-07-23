{if $p->delivery_in_stock||$additional_shipping_cost||$variation_data.guarantee}

<div class="col-12 variation_extra_info">
  {if $p->delivery_in_stock}
    <div class="variation_shipping_delay">
      <div class="variation_extra_info_icon">
        <i class="hetj-Plan-de-travail-45-copieico fa cbp-mainlink-icon"></i>
      </div>
      <div class="variation_extra_info_text">
        <strong>{l s='Shipping delay' mod='gecps'} : </strong>{$p->delivery_in_stock} {l s='Business days' mod='gecps'}
      </div>
    </div>
  {/if}
  {if $additional_shipping_cost}
    <div class="variation_shipping_cost">
      <div class="variation_extra_info_icon">
        <i class="hetj-Plan-de-travail-45-copieico fa cbp-mainlink-icon"></i>
      </div>
      <div class="variation_extra_info_text">
        <strong>{l s='Shipping cost' mod='gecps'} : </strong>{$additional_shipping_cost}<sup>*</sup>
      </div>
    </div>
  {/if}
  {if $variation_data.guarantee}
    <div class="variation_guarantee">
      <div class="variation_extra_info_icon">
        <i class="hetj-Plan-de-travail-40-copie-3ico fa cbp-mainlink-icon"></i>
      </div>
      <div class="variation_extra_info_text">
        <strong>{l s='Guarantee' mod='gecps'} : </strong>{$variation_data.guarantee}
      </div>
    </div>
  {/if}

</div>
{/if}


<div class="service_included_info">
  <h5>{l s='Service included' mod='gecps'} :</h5>
  <ul>
    <li><div><i class="far fa-smile"></i></div><span>{l s='service_included_info_1' mod='gecps'}</span></li>
    <li><div><i class="far fa-lock"></i></div><span>{l s='service_included_info_2' mod='gecps'}</span></li>
    <li><div><i class="far fa-truck"></i></div><span>{l s='service_included_info_3' mod='gecps'}</span></li>
    <li><div><i class="far fa-user-headset"></i></div><span>{l s='service_included_info_4' mod='gecps'}</span></li>
  </ul>
</div>
