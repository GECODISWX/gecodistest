{if isset($url_image)}
  <span
          {if $group_attribute.texture}
              class="color texture" style="background-image: url({$group_attribute.texture})"
          {elseif $group_attribute.html_color_code}
              class="color" style="background-color: {$group_attribute.html_color_code}"
          {else}
              class="color texture" style="background-image: url({$url_image})"

          {/if}

  ><span class="sr-only">{$group_attribute.name}</span></span><br>
  <span class="variation_price">{$variation_price}</span>
{/if}
