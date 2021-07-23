{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}


{if $iqitTheme.f_newsletter_status == 1 || $iqitTheme.f_social_status == 1}
<div id="footer-container-first" class="footer-container footer-style-2">
  <div class="container">
    <div class="row">
      {block name='hook_footer_before'}
        {hook h='displayFooterBefore'}
      {/block}
    </div>
    {* <div class="row align-items-center">

        {if $iqitTheme.f_newsletter_status == 1}
        <div class="col-sm-12 col-md-3 block-newsletter text-center">
          <h5 class="mb-3">{l s='Sign up to newsletter' d='Shop.Warehousetheme'}</h5>
        </div>
        <div class="col-sm-12 col-md-3 block-newsletter">
          {widget name="ps_emailsubscription" hook='displayFooter'}
        </div>
        <div class="col-sm-12 col-md-3 block-newsletter">
          {widget name="ps_emailsubscription" hook='displayFooter2'}
        </div>
        {/if}

        {if $iqitTheme.f_social_status == 1}
        <div class="{if $iqitTheme.f_newsletter_status == 1}col-sm-12 col-md-3{else}col{/if} block-social-links text-center">
          {include file='_elements/social-links.tpl' class='_footer'}
        </div>
        {/if}

    </div> *}

  </div>
</div>
{/if}

<div id="footer-container-main" class="footer-container footer-style-2">
  <div class="container_full">
    <div class="row">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
      <div id="footer_newsletter_col" class="col col-md block block-toggle block-iqithtmlandbanners-html js-block-toggle">
        <h5 class="block-title">{l s='Newsletter' d='Shop.Warehousetheme'}</h5>
        <p>{l s='footer_newsletter_subtitle' d='Shop.Warehousetheme'}</p>
        {widget name="ps_emailsubscription" hook='displayFooter2'}
        {widget name="ps_emailsubscription" hook='displayFooter'}
      </div>
      <div id="footer_follow_us_col" class="col col-md block block-toggle block-iqithtmlandbanners-html js-block-toggle">
        <h5 class="block-title">{l s='Follow us' d='Shop.Warehousetheme'}</h5>
        <p>{l s='footer_follow_us_subtitle' d='Shop.Warehousetheme'}</p>
        {include file='_elements/social-links.tpl' class='_footer'}
        <br><br>
        <h5 class="block-title">{l s='International' d='Shop.Warehousetheme'}</h5>
        <p>
          <a href="/fr"><img src="/img/l/FR.jpg" alt="FR"></a>
          <a href="/es"><img src="/img/l/ES.jpg" alt="ES"></a>
        </p>
      </div>
    </div>
    <div class="row">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
    </div>
  </div>
</div>

{include file='_partials/_variants/footer-copyrights-1.tpl'}
