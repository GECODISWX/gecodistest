{if $addresses}
<br>
<div class="row">
  <div class="delivery_address_holder col">
    <h3>{l s="Delivery address" mod="gecps"}</h3>
    <div class="">
      {$addresses.delivery.formatted nofilter}
    </div>
  </div>
  <div class="invoice_address_holder col">
    <h3>{l s="Invoice address" mod="gecps"}</h3>
    <div class="">
      {$addresses.invoice.formatted nofilter}
    </div>
  </div>
</div>
{/if}
